<?php

namespace app\components\customUrlManager;

use app\models\Category;
use app\models\UnderCategory;
use yii\web\UrlRuleInterface;
use yii\base\Object;
use yii\helpers\ArrayHelper;
use app\models\City;

class CityUrlRule extends Object implements UrlRuleInterface
{

    public function createUrl($manager, $route, $params)
    {

        return false;  // данное правило не применимо
    }

    public function parseRequest($manager, $request)
    {
        return false;
    }

    protected function getIndexArray()
    {
        if (!$indexCities = \Yii::$app->cache->get('list_citi_from_bd')) {
            $indexCities = ArrayHelper::index(City::find()
                ->select(['name', 'url_name'])
                ->orderBy(['name' => SORT_ASC])
                ->all(), 'url_name');

            \Yii::$app->cache->add('list_citi_from_bd', $indexCities, 600);
        }


        return [
            'indexCities' => $indexCities,
        ];
    }
}