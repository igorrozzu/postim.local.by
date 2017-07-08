<?php

namespace app\components;

use Yii;
use yii\web\Controller;
use app\models\LoginForm;

class SiteController extends Controller
{

   public function init()
   {
       if(!yii::$app->user->isGuest){
           $this->layout='mainAut';
       }

   }
}
