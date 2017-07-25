<?php
/**
 * Created by PhpStorm.
 * User: jrborisov
 * Date: 8.7.17
 * Time: 20.30
 */

namespace app\components\breadCrumb;

use yii\base\Widget;


class BreadCrumb extends Widget
{
    public $breadcrumbParams=null;


    public function run()
    {
        echo $this->render('index',['breadcrumbParams'=>$this->breadcrumbParams]);
    }

}