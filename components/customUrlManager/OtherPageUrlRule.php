<?php

namespace app\components\customUrlManager;


use app\models\OtherPage;
use yii\helpers\ArrayHelper;


class OtherPageUrlRule extends CityUrlRule
{

    public function createUrl($manager, $route, $params)
    {

        return false;  // данное правило не применимо
    }

    public function parseRequest($manager, $request)
    {
        $pathInfo = $request->getPathInfo();

        $queryParams = explode('/', $pathInfo);

        $route = '/site/other-page';

        if (isset($queryParams[0])) {

            $arrIndex = $this->getIndexArray();

            if (isset($arrIndex['indexOtherPage']['/' . $queryParams[0]])) {
                $params['url_name'] = $arrIndex['indexOtherPage']['/' . $queryParams[0]]->url_name;

                return [$route, $params];
            }

        }

        return false;
    }

    protected function getIndexArray()
    {
        $array = parent::getIndexArray();

        /*if(!$indexOtherPage = \Yii::$app->cache->get('listOtherPage')){*/
        $indexOtherPage = ArrayHelper::index(OtherPage::find()
            ->select(['url_name'])
            ->all(), 'url_name');

        /*    \Yii::$app->cache->add('listOtherPage',$indexOtherPage,600);
        }*/

        $array['indexOtherPage'] = $indexOtherPage;
        return $array;

    }
}