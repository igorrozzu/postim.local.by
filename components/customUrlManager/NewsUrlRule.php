<?php

namespace app\components\customUrlManager;

use app\models\Category;
use app\models\UnderCategory;
use yii\web\UrlRuleInterface;
use yii\base\Object;
use yii\helpers\ArrayHelper;
use app\models\City;

class NewsUrlRule extends CityUrlRule {

    public function createUrl($manager, $route, $params)
    {

        return false;  // данное правило не применимо
    }

    public function parseRequest($manager, $request)
    {
        $pathInfo = $request->getPathInfo();

        $queryParams= explode('/',$pathInfo);

        $route='/news/index';

        if(count($queryParams)==1 && $queryParams[0]=='novosti'){
            $params=[];
            return [$route,$params];
        }

        $arrIndex = $this->getIndexArray();

        if(count($queryParams)==2 && isset($arrIndex['indexCities'][$queryParams[0]]) && $queryParams[1]=='novosti' ){
            $params['city']['name']=$arrIndex['indexCities'][$queryParams[0]]['name'];
            $params['city']['url_name']=$arrIndex['indexCities'][$queryParams[0]]['url_name'];

            \Yii::$app->city->setCity(['name'=>$params['city']['name'],
                'url_name'=>$params['city']['url_name']]);

            return [$route,$params];
        }


        return false;
    }

    protected function getIndexArray()
    {
        $array= parent::getIndexArray();
        return $array;

    }
}