<?php

namespace app\controllers;

use app\components\AuthController;
use app\models\entities\NotificationUser;
use app\models\NotificationSearch;
use Yii;
use yii\data\Pagination;

class TestController extends AuthController
{
    public function actionIndex()
    {
        return $this->render('test-discount');
    }
}
