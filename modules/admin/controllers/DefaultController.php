<?php

namespace app\modules\admin\controllers;

use app\modules\admin\components\AdminDefaultController;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;

/**
 * Default controller for the `admin` module
 */
class DefaultController extends AdminDefaultController
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionSetWewefwf()
    {

        if ($tokenWewefwf = \Yii::$app->request->get('tokenWewefwf', false)) {
            $cookies = \Yii::$app->response->cookies;

            $cookie = new Cookie([
                'name' => 'tokenWewefwf',
                'value' => $tokenWewefwf,
                'domain' => $_SERVER['SERVER_NAME'],
                'expire' => time() + 3600 * 24,
            ]);
            $cookies->add($cookie);

            $this->redirect('/admin');
        } else {
            throw new NotFoundHttpException('Cтраница не найдена');
        }


    }
}
