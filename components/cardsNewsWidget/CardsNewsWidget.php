<?php
/**
 * Created by PhpStorm.
 * User: jrborisov
 * Date: 8.7.17
 * Time: 20.30
 */

namespace app\components\cardsNewsWidget;

use yii\base\Widget;


class CardsNewsWidget extends Widget
{
    public $dataprovider;

    public function run()
    {
        echo $this->render('index',['dataprovider'=>$this->dataprovider]);
    }
}