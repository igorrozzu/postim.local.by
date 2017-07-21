<?php

namespace app\components\customUrlManager;

use app\models\Category;
use app\models\UnderCategory;
use yii\web\UrlRuleInterface;
use yii\base\Object;
use yii\helpers\ArrayHelper;
use app\models\City;

class CityAndCategoryUrlRule extends Object implements UrlRuleInterface{

    public function createUrl($manager, $route, $params)
    {

        return false;  // данное правило не применимо
    }

    public function parseRequest($manager, $request)
    {
        $pathInfo = $request->getPathInfo();

        $queryParams= explode('/',$pathInfo);

        $arrIndex = $this->getIndexArrayCitiesAndCategories();



        if(isset($arrIndex['indexCities'][$queryParams[0]])){
            $params['city']['name']=$arrIndex['indexCities'][$queryParams[0]]['name'];
            $params['city']['url_name']=$arrIndex['indexCities'][$queryParams[0]]['url_name'];

            \Yii::$app->city->setCity(['name'=>$params['city']['name'],
                'url_name'=>$params['city']['url_name']]);

            if(isset($queryParams[1]) &&
                (isset($arrIndex['index_under_category'][$queryParams[1]]) ||
                    isset($arrIndex['index_category'][$queryParams[1]])
                )
            ){
                $route='/category/index';

                if(isset($arrIndex['index_category'][$queryParams[1]])){
                    $params['category']['name']=$arrIndex['index_category'][$queryParams[1]]['name'];
                    $params['category']['url_name']=$arrIndex['index_category'][$queryParams[1]]['url_name'];
                }else{
                    $params['category']['name']=$arrIndex['index_under_category'][$queryParams[1]]['category']['name'];
                    $params['category']['url_name']=$arrIndex['index_under_category'][$queryParams[1]]['category']['url_name'];

                    $params['under_category']['name']=$arrIndex['index_under_category'][$queryParams[1]]['name'];
                    $params['under_category']['url_name']=$arrIndex['index_under_category'][$queryParams[1]]['url_name'];
                }


            }elseif(!isset($queryParams[1])){
                $route='/site/index';
            }else{
                return false;
            }

            return [$route,$params];
        }


        if(isset($queryParams[0]) &&
            (isset($arrIndex['index_under_category'][$queryParams[0]]) ||
                isset($arrIndex['index_category'][$queryParams[0]])
            )
        ){
            $route='/category/index';

            if(isset($arrIndex['index_category'][$queryParams[0]])){
                $params['category']['name']=$arrIndex['index_category'][$queryParams[0]]['name'];
                $params['category']['url_name']=$arrIndex['index_category'][$queryParams[0]]['url_name'];
            }else{
                $params['category']['name']=$arrIndex['index_under_category'][$queryParams[0]]['category']['name'];
                $params['category']['url_name']=$arrIndex['index_under_category'][$queryParams[0]]['category']['url_name'];

                $params['under_category']['name']=$arrIndex['index_under_category'][$queryParams[0]]['name'];
                $params['under_category']['url_name']=$arrIndex['index_under_category'][$queryParams[0]]['url_name'];
            }

            \Yii::$app->city->setDefault();

            return [$route,$params];
        }

        return false;
    }

    private function getIndexArrayCitiesAndCategories(){
        if(!$indexCities = \Yii::$app->cache->get('list_citi_from_bd')){
            $indexCities = ArrayHelper::index(City::find()
                ->select(['name','url_name'])
                ->orderBy(['name'=>SORT_ASC])
                ->all(),'url_name');

            \Yii::$app->cache->add('list_citi_from_bd',$indexCities,600);
        }

        if(!$index_under_category =\Yii::$app->cache->get('list_under_cat_from_bd')){
            $index_under_category = ArrayHelper::index(UnderCategory::find()
                ->innerJoinWith('category')
                ->orderBy(['tbl_under_category.name'=>SORT_ASC])
                ->all(),'url_name');
            \Yii::$app->cache->add('list_under_cat_from_bd',$index_under_category,600);
        }

        if(!$index_category =\Yii::$app->cache->get('list_cat_from_bd')){
            $index_category = ArrayHelper::index(Category::find()
                ->select(['name','url_name'])
                ->orderBy(['name'=>SORT_ASC])
                ->all(),'url_name');
            \Yii::$app->cache->add('list_cat_from_bd',$index_category,600);
        }

        return [
            'indexCities' => $indexCities,
            'index_under_category' => $index_under_category,
            'index_category'=>$index_category
        ];
    }
}