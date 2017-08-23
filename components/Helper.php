<?php
namespace app\components;

use app\models\Category;
use app\models\TotalView;
use app\models\UnderCategory;
use Yii;
use yii\helpers\ArrayHelper;
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

        $featurePrice = ['average_bill2','beer_price','price_per_hour_sauna2','price_rolls'];
        if(in_array($feature['features_id'],$featurePrice)){
            return self::mb_ucasefirst($feature['value']) .' BYN';
        }
        return self::mb_ucasefirst($feature['value']);
    }

    public static function getCostFeature($feature){
        $featurePrice = ['average_bill2','beer_price','price_per_hour_sauna2','price_rolls'];
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

}