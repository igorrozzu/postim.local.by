<?php

namespace app\controllers;

use app\components\MainController;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class UserController extends MainController
{

    public function actionIndex($id)
    {
        return $this->render('index');
    }

    public function actionSettings()
    {
        if(Yii::$app->user->isGuest) {
            throw new NotFoundHttpException('Cтраница не найдена');
        }

        if(Yii::$app->request->isPost) {

            return $this->render('settings-form');
        }

        $socialBindings = Yii::$app->user->identity->socialBindings;
        return $this->render('settings-form', [
            'socialBindings' => $socialBindings
        ]);
    }
}
