<?php

namespace app\components\customUrlManager;

class DiscountsUrlRule extends CityUrlRule
{

    public function createUrl($manager, $route, $params)
    {

        return false;  // данное правило не применимо
    }

    public function parseRequest($manager, $request)
    {
        $pathInfo = $request->getPathInfo();

        $queryParams = explode('/', $pathInfo);

        $route = '/discount/read-all';

        if (count($queryParams) == 1 && $queryParams[0] == 'skidki') {
            $params = [];
            \Yii::$app->city->setDefault();
            return [$route, $params];
        }

        $arrIndex = $this->getIndexArray();

        if (count($queryParams) == 2 && isset($arrIndex['indexCities'][$queryParams[0]]) && $queryParams[1] == 'skidki') {
            $params['city']['name'] = $arrIndex['indexCities'][$queryParams[0]]['name'];
            $params['city']['url_name'] = $arrIndex['indexCities'][$queryParams[0]]['url_name'];

            \Yii::$app->city->setCity([
                'name' => $params['city']['name'],
                'url_name' => $params['city']['url_name'],
            ]);

            return [$route, $params];
        }


        return false;
    }

    protected function getIndexArray()
    {
        $array = parent::getIndexArray();
        return $array;

    }
}