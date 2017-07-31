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
    public $settings;

    public function run()
    {
        return $this->render('index', [
            'dataProvider'=> $this->dataprovider,
            'settings' => $this->settings
        ]);
    }
}