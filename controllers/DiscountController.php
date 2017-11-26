<?php
/**
 * Created by PhpStorm.
 * User: igorrozu
 * Date: 11/22/17
 * Time: 10:25 PM
 */

namespace app\controllers;


use yii\web\Controller;

class DiscountController extends Controller
{
    public function actionAdd()
    {
        $this->layout = 'mainAuth';
        return $this->render('add');
    }
}