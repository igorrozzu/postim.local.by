<?php

namespace app\controllers;

use app\components\AuthController;
use app\models\entities\NotificationUser;
use app\models\NotificationSearch;
use Yii;
use yii\data\Pagination;

class NotificationController extends AuthController
{
    public function actionIndex()
    {
        $searchModel = new NotificationSearch();
        $pagination = new Pagination([
            'pageSize' => Yii::$app->request->get('per-page', 10),
            'page' => Yii::$app->request->get('page', 1) - 1,
        ]);
        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            $pagination
        );
        $json = new \stdClass();
        $json->rendering = $this->renderPartial('index', [
            'dataProvider' => $dataProvider,
        ]);
        return $this->asJson($json);
    }

    public function actionGetCountNotifications()
    {
        return NotificationUser::getCountNotifications();
    }

    public function actionMarkAsRead()
    {
        return NotificationUser::markAsShowed();
    }
}
