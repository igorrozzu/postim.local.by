<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\components\MainController;

class SiteController extends MainController
{


    public function actionIndex()
    {
        return $this->render('index');
    }


}
