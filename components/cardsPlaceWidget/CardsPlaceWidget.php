<?php
/**
 * Created by PhpStorm.
 * User: jrborisov
 * Date: 8.7.17
 * Time: 20.30
 */

namespace app\components\cardsPlaceWidget;

use yii\base\Widget;


class CardsPlaceWidget extends Widget
{

    public function run()
    {
        echo $this->render('index');
    }
}