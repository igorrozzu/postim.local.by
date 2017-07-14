<?php

namespace app\components\customUrlManager;

use yii\web\UrlRuleInterface;
use yii\base\Object;
use yii\helpers\ArrayHelper;
use app\models\City;

class CityUrlRule extends Object implements UrlRuleInterface{

    public function createUrl($manager, $route, $params)
    {

        return false;  // данное правило не применимо
    }

    public function parseRequest($manager, $request)
    {
        $pathInfo = $request->getPathInfo();

        $queryParams= explode('/',$pathInfo);
        $queryCity='';
        $queryCategory='';

        if(count($queryParams)<2){
            $queryParams[1]='';
        }
        list($queryCity,$queryCategory) = $queryParams;

        $params=['city_name'=>'','url_name'=>''];

        if(!$indexCities = \Yii::$app->cache->get('list_citi_from_bd')){
            $indexCities = ArrayHelper::index(City::find()
                ->select(['name','url_name'])
                ->orderBy(['name'=>SORT_ASC])
                ->all(),'url_name');

            \Yii::$app->cache->add('list_citi_from_bd',$indexCities,600);
        }



        if(isset($indexCities[$queryCity])){
            $params['city_name']=$indexCities[$queryCity]['name'];
            $params['url_name']=$indexCities[$queryCity]['url_name'];

            if($queryCategory){
                $route='/category/index';
                $params['name_category']=$queryCategory;
            }else{
                $route='/site/index';
            }

            return [$route,$params];
        }

        return false;
    }
}