<?php

namespace app\widgets\cardsDiscounts;

use yii\base\Widget;


class CardsDiscounts extends Widget
{
    public $dataProvider;
    public $settings;

    public function run()
    {
        return $this->render('index', [
            'dataProvider' => $this->dataProvider,
            'settings' => $this->settings,
        ]);
    }
}