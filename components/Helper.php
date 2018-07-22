<?php
namespace app\components;

use app\models\Category;
use app\models\TotalView;
use app\models\UnderCategory;
use app\models\WorkingHours;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Request;

class Helper{

    public static function addViews(TotalView $totalView){
        $session = Yii::$app->session;

        if($session->get('totalView_'.$totalView['id'])==null){
            $totalView->updateCounters(['count' => 1]);
            $session->set('totalView_'.$totalView['id'],true);
        }
    }

    public static function getDomainNameByUrl($url){
        return self::mb_ucasefirst(preg_replace('/(https?:\/\/)|(www.)/','',$url));
    }

    public static function getShortNameDayById($id){
        $mapNameDay=['Вск','Пн','Вт','Ср','Чт','Пт','Сб','Вск'];
        if(isset($mapNameDay[$id])){
            return $mapNameDay[$id];
        }else{
            return '';
        }
    }


    public static $feature_map = [
        'average_bill2' => 'средний чек: ',
        'type_cuisine'=>'кухня: ',
        'beer_price'=>'бокал пива: ',
        'price_category'=>'ценовая категокрия: '
    ];

    public static function getFeature($features)
    {

        if ($features->rubrics) {
            foreach ($features->rubrics as $rubric){
                ?>
                    <div class="info-row">
                        <div class="left-block-f">
                            <div class="title-info-card"><?=self::mb_ucasefirst($rubric['name'])?></div>
                            <div class="block-inside">
                                <ul class="lists-features">
                                    <?php foreach ($rubric['values'] as $feature):?>
                                        <li class="lists-feature"><?=self::mb_ucasefirst($feature['name'])?></li>
                                    <?php endforeach;?>
                                </ul>
                            </div>
                        </div>
                        <div class="right-block-f">
                            <div class="btn-info-card"></div>
                        </div>
                    </div>
                <?php

            }
        }

        if ($features->features) {
                ?>
                <div class="info-row">
                    <div class="left-block-f">
                        <div class="title-info-card">Особености</div>
                        <div class="block-inside">
                            <ul class="lists-features">
                                <?php foreach ($features->features as $feature):?>
                                    <?php if($feature['type']==1):?>
                                    <li class="lists-feature"><?=self::mb_ucasefirst($feature['name'])?></li>
                                    <?php else:?>
                                        <li class="lists-feature"><?=self::mb_ucasefirst($feature['name'])?>: <?=self::getPriceFeature($feature)?></li>
                                    <?php endif;?>
                                <?php endforeach;?>
                            </ul>
                        </div>
                    </div>
                    <div class="right-block-f">
                        <div class="btn-info-card"></div>
                    </div>
                </div>
                <?php
        }

    }

    public static function getPriceFeature($feature){

        $featurePrice = ['average_bill2','beer_price','price_per_hour_sauna2','price_rolls','price_per_month2','price_room','price_cup_cappuccino','price_evening'];
        if(in_array($feature['features_id'],$featurePrice)){
            return self::mb_ucasefirst($feature['value']) .' BYN';
        }
        return self::mb_ucasefirst($feature['value']);
    }

    public static function getCostFeature($feature){
        $featurePrice = ['average_bill2','beer_price','price_per_hour_sauna2','price_rolls','price_per_month2','price_room','price_cup_cappuccino','price_evening'];
        if(in_array($feature['id'],$featurePrice)){
            return 'BYN';
        }
        return '';
    }

    public static function mb_ucasefirst($str, $encoding='UTF-8'){
        $str = mb_ereg_replace('^[\ ]+', '', $str);
        $str = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding).
            mb_substr($str, 1, mb_strlen($str), $encoding);
        return $str;
    }

    public static function createUrlWithSelfParams($selfParams,$params=false){

        $request = Yii::$app->getRequest();

        $queryParams = $request instanceof Request ? $request->getQueryParams() : [];
        if($params){
            $queryParams= ArrayHelper::merge($queryParams,$params);
        }
        $newParams=[];
        foreach ($queryParams as $key => $param){
            if(isset($selfParams[$key]) && $selfParams[$key]){
                $newParams[$key]=$param;
            }
        }
        $newParams[0]=Yii::$app->request->getPathInfo();

        $urlManager = Yii::$app->getUrlManager();
        return $urlManager->createUrl($newParams);

    }


    public static function getDistanceBP($φA, $λA, $φB, $λB){

        $lat1 = $φA * M_PI / 180;
        $lat2 = $φB * M_PI / 180;
        $long1 = $λA * M_PI / 180;
        $long2 = $λB * M_PI / 180;

        $cl1 = cos($lat1);
        $cl2 = cos($lat2);
        $sl1 = sin($lat1);
        $sl2 = sin($lat2);
        $delta = $long2 - $long1;
        $cdelta = cos($delta);
        $sdelta = sin($delta);


        $y = sqrt(pow($cl2 * $sdelta, 2) + pow($cl1 * $sl2 - $sl1 * $cl2 * $cdelta, 2));
        $x = $sl1 * $sl2 + $cl1 * $cl2 * $cdelta;


        $ad = atan2($y, $x);
        $dist = (int)($ad * 6372795);

        return $dist;
    }

    public static function saveQueryForMap($query){
        $queryReplaceTime = preg_replace('/WHERE \("tbl_posts"\."date" <= [0-9]+\)/','',$query);
        $key=md5(serialize($queryReplaceTime));
        Yii::$app->cache->set($key,serialize($query));
        return $key;
    }

    public static function parserWorktime($workingHours):array {
        $arrayResult = [1=>['day_type'=>1,'time_start'=>null,'time_finish'=>null],
			['day_type'=>2,'time_start'=>null,'time_finish'=>null],
			['day_type'=>3,'time_start'=>null,'time_finish'=>null],
			['day_type'=>4,'time_start'=>null,'time_finish'=>null],
			['day_type'=>5,'time_start'=>null,'time_finish'=>null],
			['day_type'=>6,'time_start'=>null,'time_finish'=>null],
			['day_type'=>7,'time_start'=>null,'time_finish'=>null],
        ];

        foreach ($workingHours as $workingHour){
            $arrayResult[$workingHour['day_type']]['time_start'] = Yii::$app->formatter->asTime($workingHour['time_start'], 'HH:mm');
            $arrayResult[$workingHour['day_type']]['time_finish'] = Yii::$app->formatter->asTime($workingHour['time_finish'], 'HH:mm');
        }

        return $arrayResult;
    }

    public static function parserForEditor(string $html, bool $editorDefault = false):string
    {
		$insertItems = \phpQuery::newDocument($html);

		$containerEditor = pq('<div></div>');

		$numberItem = 0;

		foreach ($insertItems['.insert-item'] as $insertItem){
            if(pq($insertItem)->hasClass('video')){
                $tmpContainerItem = pq('<div class="item-editor item container-insert">
                                            <div class="container-toolbar"> 
                                                <div class="title-toolbar">Видео</div> 
                                                <div class="btns-toolbar-container">
                                                    <div class="btn-toolbar-top"></div> 
                                                    <div class="btn-toolbar-down"></div> 
                                                    <div class="btn-toolbar-close"></div>
                                                </div>
                                            </div>
                                            <div class="block-insert video" style="display: block;"></div>
                                    </div>');
                $insert = pq($tmpContainerItem->find('.block-insert.video'));
                $insert->html(pq($insertItem)->html());
                $containerEditor->append($tmpContainerItem);

            }elseif (pq($insertItem)->find('.block-photo-post')->length()){

                $tmpContainerItem = pq('<div class="item-editor item container-insert">
                                            <div class="container-toolbar"> 
                                                <div class="title-toolbar">Фото</div> 
                                                <div class="btns-toolbar-container">
                                                    <div class="btn-toolbar-top"></div> 
                                                    <div class="btn-toolbar-down"></div> 
                                                    <div class="btn-toolbar-close"></div>
                                                </div>
                                            </div>
                                            <div class="block-insert js-photo" style="display: block;"></div>
                                    </div>');
                $insert = pq($tmpContainerItem->find('.block-insert.js-photo'));
                $insert->html(pq($insertItem)->html());
                $insert->find('.block-photo-post')
                    ->removeClass('block-photo-post')
                    ->addClass('photo-item');

                $input = pq('<input placeholder="Подпись к фото" class="img-source">');

                if($insert->find('.photo-desc')->length()){
                    $input->val($insert->find('.photo-desc')->text());
                    $insert->find('.photo-desc')->remove();
                }

                $insert->find('.photo-item')->append($input);

                $containerEditor->append($tmpContainerItem);

            }

            else{
                $tmpContainerItem = pq('<div class="item  container-editor"><div class="editable"></div></div>');
                if($editorDefault && $numberItem++ == 0){
					$tmpContainerItem->addClass('item-editor-default');
                }else{
                    $tmpContainerItem = pq('<div class="item-editor item container-editor"><div class="container-toolbar"> <div class="title-toolbar">Текст</div> <div class="btns-toolbar-container"><div class="btn-toolbar-top"></div> <div class="btn-toolbar-down"></div> <div class="btn-toolbar-close"></div></div></div> <div class="editable"></div></div>');
                }
                $editable = pq($tmpContainerItem->find('.editable'));
                $editable->html(pq($insertItem)->html());
                $containerEditor->append($tmpContainerItem);
            }
        }

        return $containerEditor->html();
    }

    public  static function getTextMarkReviews(int $mark):string {
        switch ($mark){
			case 1:
				return 'Очень плохо';
				break;
			case 2:
				return 'Не понравилось';
				break;
			case 3:
				return 'Нормально';
				break;
			case 4:
				return 'Хорошо';
				break;
			case 5:
				return 'Отлично';
				break;
			default :
				return 'Поставьте вашу оценку';
				break;
        }
    }

    public static function checkValidUrl(string $currentUrl, string $validUrl){

        if($currentUrl == $validUrl){
            return true;
        }else{
            return Yii::$app->getResponse()->redirect(array($validUrl), 301);
        }

    }

    public static function getMetro(){

        $metro = [['id' => 'Уручье', 'name' => 'Уручье'],
            ['id' => 'Борисовский тракт', 'name' => 'Борисовский тракт'],
            ['id' => 'Восток', 'name' => 'Восток'],
            ['id' => 'Московская', 'name' => 'Московская'],
            ['id' => 'Парк Челюскинцев', 'name' => 'Парк Челюскинцев'],
            ['id' => 'Академия наук', 'name' => 'Академия наук'],
            ['id' => 'Площадь Якуба Коласа', 'name' => 'Площадь Якуба Коласа'],
            ['id' => 'Площадь Победы', 'name' => 'Площадь Победы'],
            ['id' => 'Октябрьская', 'name' => 'Октябрьская'],
            ['id' => 'Площадь Ленина', 'name' => 'Площадь Ленина'],
            ['id' => 'Институт Культуры', 'name' => 'Институт Культуры'],
            ['id' => 'Грушевка', 'name' => 'Грушевка'],
            ['id' => 'Петровщина', 'name' => 'Петровщина'],
            ['id' => 'Михалово', 'name' => 'Михалово'],
            ['id' => 'Малиновка', 'name' => 'Малиновка'],
            ['id' => 'Каменная Горка', 'name' => 'Каменная Горка'],
            ['id' => 'Кунцевщина', 'name' => 'Кунцевщина'],
            ['id' => 'Спортивная', 'name' => 'Спортивная'],
            ['id' => 'Пушкинская', 'name' => 'Пушкинская'],
            ['id' => 'Молодёжная', 'name' => 'Молодёжная'],
            ['id' => 'Фрунзенская', 'name' => 'Фрунзенская'],
            ['id' => 'Немига', 'name' => 'Немига'],
            ['id' => 'Купаловская', 'name' => 'Купаловская'],
            ['id' => 'Первомайская', 'name' => 'Первомайская'],
            ['id' => 'Пролетарская', 'name' => 'Пролетарская'],
            ['id' => 'Тракторный завод', 'name' => 'Тракторный завод'],
            ['id' => 'Партизанская', 'name' => 'Партизанская'],
            ['id' => 'Автозаводская', 'name' => 'Автозаводская'],
            ['id' => 'Могилёвская', 'name' => 'Могилёвская'],
        ];

        ArrayHelper::multisort($metro,'id');

        return $metro;

    }

}