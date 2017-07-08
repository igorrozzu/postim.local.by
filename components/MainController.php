<?php

namespace app\components;

use Yii;
use yii\web\Controller;
use app\models\LoginForm;

class MainController extends Controller
{

   public function init()
   {
       if(!yii::$app->user->isGuest){
           $this->layout='mainAut';
       }

   }
}
