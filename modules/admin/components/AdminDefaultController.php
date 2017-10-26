<?php

namespace app\modules\admin\components;

use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Default controller for the `admin` module
 */
class AdminDefaultController extends Controller
{

    public function init()
    {
        parent::init();

        if (\Yii::$app->user->isGuest ||
            \Yii::$app->user->identity->role < 2 ||
            !in_array(\Yii::$app->user->getId(),[15,16])
        ){
            throw new NotFoundHttpException('Cтраница не найдена');
        }

        $this->layout = 'main';

    }
}
