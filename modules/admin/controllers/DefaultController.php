<?php

namespace app\modules\admin\controllers;

use app\modules\admin\components\AdminDefaultController;
use yii\web\Controller;

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
}
