<?php
/**
 * Created by PhpStorm.
 * User: jrborisov
 * Date: 8.7.17
 * Time: 20.30
 */

namespace app\components\socialWidgets;

use yii\base\Widget;


class SocialWidget extends Widget
{

    public function run()
    {
        echo $this->render('index');
    }
}