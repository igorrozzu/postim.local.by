<?php

namespace app\modules\admin\controllers;

use app\modules\admin\components\AdminDefaultController;
use yii\data\Pagination;
use yii\web\Controller;

/**
 * Default controller for the `admin` module
 */
class ModerationController extends AdminDefaultController
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {


    }

    public function actionComplaints(){
        return $this->render('complaints');
    }



}
