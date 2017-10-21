<?php
namespace app\components;

use yii;


class City extends  yii\base\Model {

    private $selected_city;

    public $name;
    public $url_name;

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string'],
            [['url_name'], 'string', 'max' => 30]
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Имя города',
            'url_name' => 'Ссылка на город',
        ];
    }

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $cookies =Yii::$app->request->cookies;
        $this->selected_city= yii\helpers\Json::decode($cookies->getValue('selected_city', yii\helpers\Json::encode(['name'=>'Беларусь','url_name'=>''])));
        if(isset($this->selected_city['name']) && isset($this->selected_city['url_name'])){
            $this->name=$this->selected_city['name'];
            $this->url_name=$this->selected_city['url_name'];
            if(!$this->validate()){
                $this->setDefault();
            }
        }else{
            $this->setDefault();
        }

    }


    public function setCity($city){
        if(isset($city['name'])&& isset($city['url_name'])){
            $this->name=$city['name'];
            $this->url_name=$city['url_name'];
            if($this->validate()){
                $this->selected_city=['name'=>$this->name,'url_name'=>$this->url_name];
                $cookies = Yii::$app->response->cookies;

                $cookie = new yii\web\Cookie([
                    'name' => 'selected_city',
                    'value' => yii\helpers\Json::encode($city),
                    'domain'=>$_SERVER['SERVER_NAME'],
                    'expire' => time() + 86400 * 365,
                ]);
                $cookies->add($cookie);
            }
        }

    }

    public function getSelected_city(){
        return $this->selected_city;
    }

    public function setDefault(){
        $this->name='Буларусь';
        $this->url_name='';
        $this->selected_city=['name'=>'Беларусь','url_name'=>''];
        $this->setCity($this->selected_city);
    }

    public function getAllIndexCities(){
        if(!$indexCities = \Yii::$app->cache->get('list_citi_from_bd')){
            $indexCities = yii\helpers\ArrayHelper::index(\app\models\City::find()
                ->select(['name','url_name'])
                ->orderBy(['name'=>SORT_ASC])
                ->all(),'url_name');

            \Yii::$app->cache->add('list_citi_from_bd',$indexCities,600);
        }

        return $indexCities;
    }



}