<?php

namespace app\components;


use yii\web\NotFoundHttpException;

class AuthController extends MainController
{
   public function init()
   {
       parent::init();

       if (\Yii::$app->user->isGuest) {
           throw new NotFoundHttpException('Cтраница не найдена');
       }

   }
}
