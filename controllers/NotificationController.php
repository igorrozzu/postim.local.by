<?php

namespace app\controllers;

use app\components\MainController;
use app\models\Notification;
use app\models\NotificationSearch;
use Yii;
use yii\data\Pagination;

class NotificationController extends MainController
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
        $notifications = $dataProvider->getModels();
        Notification::markAsRead($notifications);
        $json = new \stdClass();
        $json->rendering = $this->renderPartial('index', [
            'dataProvider' => $dataProvider,
            'notifications' => $notifications
        ]);
        $json->notifCount = Notification::getCountNotifications();
        return $this->asJson($json);
    }

    public function actionGetCountNotifications()
    {
        return Notification::getCountNotifications();
    }
}
