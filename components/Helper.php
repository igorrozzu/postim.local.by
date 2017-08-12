<?php
namespace app\components;

use app\models\TotalView;
use Yii;

class Helper{

    public static function addViews(TotalView $totalView){
        $session = Yii::$app->session;

        if($session->get('totalView_'.$totalView['id'])==null){
            $totalView->updateCounters(['count' => 1]);
            $session->set('totalView_'.$totalView['id'],true);
        }
    }

    public static function getDomainNameByUrl($url){
        return preg_replace('/https?:\/\//','',$url);
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
    public static function getFeature($key,$feature){


       if(isset(self::$feature_map[$key])){
           if(is_array($feature)){
               return self::$feature_map[$key].implode(', ',$feature);
           }
           return self::$feature_map[$key].$feature;
       }else{
           if(is_array($feature)){
               return implode(', ',$feature);
           }
           return $feature;
       }
    }
}