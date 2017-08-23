<?php

namespace app\commands;

use app\models\Features;
use app\models\News;
use app\models\PostFeatures;
use app\models\PostInfo;
use app\models\Posts;
use app\models\PostUnderCategory;
use app\models\TotalView;
use app\models\UnderCategoryFeatures;
use app\models\WorkingHours;
use dosamigos\transliterator\TransliteratorHelper;
use linslin\yii2\curl\Curl;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\console\Controller;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use Yii;

class ParserController extends Controller{

    private $post=null;
    private $post_info=null;
    private $working_hours=[];
    private $nameFile='';

    private $post_features=[];
    private $under_category;

    public function actionIndex($text,$under_category){
        $this->under_category =$under_category;
        $this->nameFile= str_replace('/','',TransliteratorHelper::process(trim($text)).'.txt');

        $curl = new Curl();
        $responseJson = $curl->setGetParams([
            'text' => $text,
            'type' => 'biz',
            'lang' => 'ru_RU',
            'results' => 500,
            'skip' => 1,
            'bbox'=>'53.839823,27.413641~53.977871,27.669759',
            'rspn'=>1,
            'apikey' => '146ee555-76f9-47b3-a871-37bb1180da02',
        ])
            ->get('https://search-maps.yandex.ru/v1/');
        $response = Json::decode($responseJson);

        $this->parser($response);
    }

    private function parser($data){

        $number=0;
        foreach ($data['features'] as $feature){
            echo "Обработана :".$number++."\n\r";
            $dataInfoPlace = $feature['properties']['CompanyMetaData'];
            $dataLatLon=$feature['geometry']['coordinates']??null;

            $this->post= new Posts();
            $this->post_info = new PostInfo();
            $this->working_hours=[];
            $this->post_features=[];

            foreach ($dataInfoPlace as $key=>$value){
                if(method_exists($this,$key)){
                    $this->{$key}($value);
                }
            }

            $this->save($dataLatLon);
        }
    }

    private function save($latLon){
        $under_category = $this->under_category;
        $transaction = Yii::$app->db->beginTransaction();
            if($postFind = Posts::find()->where(['address'=>$this->post->address,'data'=>$this->post->data])->one()){

                if(!PostUnderCategory::find()->where(['post_id'=>$postFind->id,'under_category_id'=>$under_category])->one()){
                    $post_under_category =  new PostUnderCategory(['post_id'=>$postFind->id,'under_category_id'=>$under_category]);
                    $post_under_category->save();
                    $transaction->commit();
                }else{
                    $transaction->rollback();
                }

            }else{
                $total_view = new TotalView(['count'=>0]);
                if($total_view->save()){
                    $this->post->city_id=90;
                    $this->post->date=time();
                    $this->post->status=1;
                    $this->post->user_id=15;
                    $this->post->latlon=$latLon;
                    $this->post->cover='/post-img/default.png';
                    $this->post->rating=0;
                    $this->post->count_favorites=0;
                    $this->post->count_reviews=0;
                    $this->post->total_view_id=$total_view->id;
                    try{

                        if($this->post->save()){

                            $post_under_category =  new PostUnderCategory(['post_id'=>$this->post->id,'under_category_id'=>$under_category]);
                            $post_under_category->save();

                            foreach ($this->post_features as $feature){
                                $feature->post_id=$this->post->id;
                                $feature->save();
                            }

                            $this->post_info->post_id=$this->post->id;
                            $this->post_info->editors=[15];
                            if($this->post_info->save()){
                                foreach ($this->working_hours as $working_hour){
                                    $working_hour->post_id = $this->post->id;
                                    $working_hour->save();

                                }
                                echo 'пост и информация были сохранены :'.$this->post->id."\n\r";
                                $transaction->commit();
                            }else{
                                echo "информация не были сохранены :".$this->post->id."\n\r";
                                $transaction->rollback();
                            }
                        }else{
                            echo "пост и информация не были сохранены :\n\r";
                            $transaction->rollback();
                        }

                    }catch (\ErrorException $exception){
                        $transaction->rollback();
                    }

                }
            }



    }

    private function url($value){
        $this->post_info->web_site = $value;
    }

    private function name($value){
        try{
            $this->post->data=$value;
        }catch (ErrorException $exception){
            echo  $exception->getMessage();
        }
    }

    private function address($value){
        try{
            $this->post->address=$value;
        }catch (ErrorException $exception){
            echo  $exception->getMessage();
        }
    }

    private function Phones($values){
        $array = [];
        foreach ($values as $value){
            array_push($array,$value['formatted']);
        }
        $this->post_info->phones = $array;
    }

    private function Hours($values){
        $array_work_time=[];
        foreach ($values['Availabilities'] as $availab){
            foreach ($availab as $day_name=>$is_work){
                if(!isset($availab['Intervals'])){
                    if(isset($availab['TwentyFourHours']) && $availab['TwentyFourHours']){
                        $timestamp_start =Yii::$app->formatter->asTimestamp('24:00');
                        $time_start = idate('H',$timestamp_start)*3600+idate('i',$timestamp_start)*60+idate('s',$timestamp_start);
                        $timestamp_start_finish =Yii::$app->formatter->asTimestamp('24:00');
                        $time_finish =idate('H',$timestamp_start_finish)*3600+idate('i',$timestamp_start_finish)*60+idate('s',$timestamp_start_finish);
                    }else{
                        $time_finish=null;
                        $time_start=null;
                    }
                }else{
                    $timestamp_start =Yii::$app->formatter->asTimestamp($availab['Intervals'][0]['from']);
                    $time_start = idate('H',$timestamp_start)*3600+idate('i',$timestamp_start)*60+idate('s',$timestamp_start);
                    $timestamp_start_finish =Yii::$app->formatter->asTimestamp($availab['Intervals'][0]['to']);
                    $time_finish =idate('H',$timestamp_start_finish)*3600+idate('i',$timestamp_start_finish)*60+idate('s',$timestamp_start_finish);
                }


                if($day_name=='Everyday'){
                    for($i=1;$i<8;$i++){
                        $workingHours = new WorkingHours();
                        $workingHours->day_type=$i;
                        $workingHours->time_start = $time_start;
                        $workingHours->time_finish=$time_finish;
                        array_push($array_work_time,$workingHours);
                    }
                    break;
                }

                if(in_array($day_name,['Monday','Tuesday','Wednesday',
                                        'Thursday','Friday','Saturday','Sunday'])  && $is_work)
                {
                    $day_name_type_map=['Tuesday'=>2,
                        'Monday'=>1,'Wednesday'=>3,'Thursday'=>4,
                        'Friday'=>5,'Saturday'=>6,'Sunday'=>7];

                    $workingHours = new WorkingHours();
                    $workingHours->day_type=$day_name_type_map[$day_name];
                    $workingHours->time_start = $time_start;
                    $workingHours->time_finish=$time_finish;
                    array_push($array_work_time,$workingHours);
                }

            }
        }

        $this->working_hours=$array_work_time;
    }

    private function Features($values){
        $arrayFeatures=[];

        foreach ($values as $value){
            if($value['type']=='bool' && $value['value']){
                $this->saveFeatures($value,1);
            }
            if($value['type']=='text'){
                $this->saveFeatures($value,2);
            }
            if($value['type']=='enum'){
                if(in_array($value['id'],['type_public_catering'])){
                    continue;
                }
               $this->saveFeaturesArray($value);

            }

        }

    }

    private function saveFeatures($value,$type){


        if($findFeatures = Features::find()->where(['id'=>$value['id']])->one()){
            if($type>1){
                $price=floatval($value['value']);
                $postFeatures = new PostFeatures(['value'=>$price]);
            }else{
                $postFeatures = new PostFeatures(['value'=>1]);
            }
            $postFeatures->features_id=$findFeatures->id;

        }else{
            $features = new Features(['id'=>$value['id'],'type'=>$type,'filter_status'=>0,'name'=>$value['name']]);
            $features->save();
            if($type>1){
                $price=floatval($value['value']);
                $postFeatures = new PostFeatures(['value'=>$price]);
            }else{
                $postFeatures = new PostFeatures(['value'=>1]);
            }

            $postFeatures->features_id=$features->id;
        }
        array_push($this->post_features,$postFeatures);
    }

    private function saveFeaturesArray($value){

        if($findFeatures = Features::find()->where(['id'=>$value['id']])->one()){
            $postFeatures = new PostFeatures(['value'=>1]);
            $postFeatures->features_id=$findFeatures->id;
            array_push($this->post_features,$postFeatures);
            foreach ($value['values'] as $enumValue){
                $postFeaturesIns = new PostFeatures(['value'=>1]);
                if($findFeaturesAr = Features::find()->where(['id'=>$enumValue['id']])->one()){
                    $postFeaturesIns->features_id=$findFeaturesAr->id;
                    $postFeaturesIns->features_main_id=$findFeaturesAr->main_features;
                }else{
                    $FeaturesAr = new Features(['id'=>$enumValue['id'],'type'=>1,'filter_status'=>0,'name'=>$enumValue['value'],'main_features'=>$findFeatures->id]);
                    $FeaturesAr->save();
                    $postFeaturesIns->features_id=$FeaturesAr->id;
                    $postFeaturesIns->features_main_id=$FeaturesAr->main_features;
                }
                array_push($this->post_features,$postFeaturesIns);

            }
        }else{
            if(!isset($value['name'])){
                return false;
            }
            $features = new Features(['id'=>$value['id'],'type'=>3,'filter_status'=>0,'name'=>$value['name']]);
            $features->save();
            $postFeatures = new PostFeatures(['value'=>1]);
            $postFeatures->features_id=$features->id;
            array_push($this->post_features,$postFeatures);

            foreach ($value['values'] as $enumValue){
                $postFeaturesIns = new PostFeatures(['value'=>1]);
                if($findFeaturesAr = Features::find()->where(['id'=>$enumValue['id']])->one()){
                    $postFeaturesIns->features_id=$findFeaturesAr->id;
                    $postFeaturesIns->features_main_id=$findFeaturesAr->main_features;
                }else{
                    $FeaturesAr = new Features(['id'=>$enumValue['id'],'type'=>1,'filter_status'=>0,'name'=>$enumValue['value'],'main_features'=>$features->id]);
                    $FeaturesAr->save();
                    $postFeaturesIns->features_id=$FeaturesAr->id;
                    $postFeaturesIns->features_main_id=$FeaturesAr->main_features;
                }
                array_push($this->post_features,$postFeaturesIns);

            }


        }

    }

    private function saveToJsonFile($value){
        $data=[];
        if(file_exists(Yii::getAlias('@app/web/tmp/'.$this->nameFile))){
            $preDataFromFile=file_get_contents(Yii::getAlias('@app/web/tmp/'.$this->nameFile));
            $data = Json::decode($preDataFromFile);
        }

        if(isset($data[$value['id']])){
            $data[$value['id']]['count']++;
        }else{
            $data[$value['id']]['count']=1;
            if(isset($value['name'])){
                $data[$value['id']]['value']=$value['name'];
            }else{
                $data[$value['id']]['value']=true;
            }

        }

        file_put_contents(Yii::getAlias('@app/web/tmp/'.$this->nameFile),Json::encode($data));
    }

    private function Links($values){
        $map_social_links=['#facebook'=>'fb','#instagram'=>'inst',
            '#vkontakte'=>'vk','#twitter'=>'tw','#odnoklassniki'=>'ok'];
        $social=[];

        foreach ($values as $value){


            if($value['type']=='social' && isset($value['aref']) && in_array($value['aref'],['#facebook',
                '#instagram', '#vkontakte', '#twitter','#odnoklassniki'])
            ){
                $social[$map_social_links[$value['aref']]]=$value['href'];
            }
        }
        if($social){
            $this->post_info->social_networks=$social;
        }
    }

    public function actionFilter(){
        $files=[
            'Antikafe v minske.txt',
            'Bar i pab v minske.txt',
            'dostavka edy v minske.txt',
            'Kafe-konditerskie  Kofeyni v Minske.txt',
            'Kafe v minske.txt',
            'kalyannye v minske.txt',
            'Konditerskie i kulinarii v Minske.txt',
            'Piccerii v minske.txt',
            'Restorany v minske.txt',
            'stolovye v minske.txt',
            'sushi bary v minske.txt',
        ];

        foreach ($files as $fileName){
            $data=Json::decode(file_get_contents(Yii::getAlias('@app/web/tmp/'.$fileName)));
            $newData=[];
            if(!$data) continue;
            foreach ($data[0] as $key => $datum){
                if($datum['count']>10){
                    $newData[$key]=$datum;
                }
            }
            file_put_contents(Yii::getAlias('@app/web/tmp/'.$fileName),Json::encode($newData));

        }
    }

    public function actionSetFilter($under_category_id , $features_id){
        $underCategoryFeature = new UnderCategoryFeatures([
            'under_category_id'=>$under_category_id,
            'features_id'=>$features_id]);
        $underCategoryFeature->save();
    }

    public function actionConvertPrice(){
        $allModels = PostFeatures::find()
            ->where(['features_id'=>'average_bill2'])
            ->andWhere(['>','value',10000])
            ->all();
        foreach ($allModels as $model){
            $model->value = $model->value / 10000;
            $model->update();
        }
    }
}