<?php

namespace app\controllers;

use app\components\MainController;

class TestController extends MainController
{
    public function actionIndex()
    {
        return $this->render('test-discount');
    }
}
