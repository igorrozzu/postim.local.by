<?php

namespace app\components\customUrlManager;

use app\models\Category;
use app\models\UnderCategory;
use yii\web\UrlRuleInterface;
use yii\base\Object;
use yii\helpers\ArrayHelper;
use app\models\City;
use Yii;

class CityAndCategoryUrlRule extends CityUrlRule {

    public function createUrl($manager, $route, $params)
    {

        return false;  // данное правило не применимо
    }

    public function parseRequest($manager, $request)
    {
        $pathInfo = $request->getPathInfo();

        $queryParams= explode('/',$pathInfo);

        $arrIndex = $this->getIndexArray();



        if(isset($arrIndex['indexCities'][$queryParams[0]])){
            $params['city']['name']=$arrIndex['indexCities'][$queryParams[0]]['name'];
            $params['city']['url_name']=$arrIndex['indexCities'][$queryParams[0]]['url_name'];

            \Yii::$app->city->setCity(['name'=>$params['city']['name'],
                'url_name'=>$params['city']['url_name']]);

            if(isset($queryParams[1]) &&
                Yii::$app->category->getUnderCategoryByName($queryParams[1]??false) ||
                Yii::$app->category->getCategoryByName($queryParams[1]??false)
            ){
                $route='/category/index';

                if(Yii::$app->category->getCategoryByName($queryParams[1])){
                    $params['category']=Yii::$app->category->getCategoryByName($queryParams[1]??false);
                }else{
                    $params['category'] = Yii::$app->category->getUnderCategoryByName($queryParams[1]??false)['category'];
                    $params['under_category']=Yii::$app->category->getUnderCategoryByName($queryParams[1]??false);
                }


            }elseif(!isset($queryParams[1])){
                $route='/site/index';
            }else{
                return false;
            }

            return [$route,$params];
        }


        if(isset($queryParams[0]) &&
            (Yii::$app->category->getUnderCategoryByName($queryParams[0]) ||
                Yii::$app->category->getCategoryByName($queryParams[0])
            )
        ){
            $route='/category/index';

            if(Yii::$app->category->getCategoryByName($queryParams[0])){
                $params['category']=Yii::$app->category->getCategoryByName($queryParams[0]);
            }else{
                $params['category'] = Yii::$app->category->getUnderCategoryByName($queryParams[0])['category'];
                $params['under_category']=Yii::$app->category->getUnderCategoryByName($queryParams[0]);
            }

            \Yii::$app->city->setDefault();

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