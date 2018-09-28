<?php

namespace app\components;

use Yii;
use yii\web\Link;
use yii\web\Request;

class Pagination extends \yii\data\Pagination
{

    public $selfParams = [
        'moderation' => true,
    ];

    public function createUrl($page, $pageSize = null, $absolute = false)
    {
        $page = (int)$page;
        $pageSize = (int)$pageSize;
        if (($params = $this->params) === null) {
            $request = Yii::$app->getRequest();
            $params = $request instanceof Request ? $request->getQueryParams() : [];
            $params = $this->trimParams($params);
        }
        if ($page > 0 || $page == 0 && $this->forcePageParam) {
            $params[$this->pageParam] = $page + 1;
        } else {
            unset($params[$this->pageParam]);
        }
        if ($pageSize <= 0) {
            $pageSize = $this->getPageSize();
        }
        if ($pageSize != $this->defaultPageSize) {
            $params[$this->pageSizeParam] = $pageSize;
        } else {
            unset($params[$this->pageSizeParam]);
        }
        $params[0] = $this->route === null ? Yii::$app->controller->getRoute() : $this->route;
        $urlManager = $this->urlManager === null ? Yii::$app->getUrlManager() : $this->urlManager;
        if ($absolute) {
            return $urlManager->createAbsoluteUrl($params);
        } else {
            return $urlManager->createUrl($params);
        }
    }

    private function trimParams($params)
    {
        $resultParams = [];

        foreach ($params as $key => $param) {
            if (isset($this->selfParams[$key]) && $this->selfParams[$key]) {
                $resultParams[$key] = $param;
            }
        }

        return $resultParams;
    }
}