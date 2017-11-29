<?php
/**
 * Created by PhpStorm.
 * User: igorrozu
 * Date: 11/22/17
 * Time: 10:25 PM
 */

namespace app\controllers;


use app\components\MainController;
use yii\web\Controller;

class DiscountController extends MainController
{
    public function actionAdd()
    {
        return $this->render('add');
    }

    public function actionOrder()
    {
        return $this->render('order');
    }

    public function actionMegamoney()
    {
        return $this->render('basket-lack-of-mega-money');
    }

    public function actionMoney()
    {
        return $this->render('basket-lack-of-money');
    }
}