<?php

namespace app\modules\admin\controllers;

use app\components\Pagination;
use app\modules\admin\components\AdminDefaultController;
use app\modules\admin\models\Features;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

/**
 * Default controller for the `admin` module
 */
class FeaturesController extends AdminDefaultController
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {

        $model = new Features();

        return $this->render('index',['model'=>$model]);

    }


}
