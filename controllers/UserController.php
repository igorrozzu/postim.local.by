<?php

namespace app\controllers;

use app\components\MainController;
use Yii;
use yii\web\Controller;

class UserController extends MainController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionSettings(){
        return $this->render('index');
    }


}
