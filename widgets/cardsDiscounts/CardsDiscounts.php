<?php

namespace app\widgets\cardsDiscounts;

use yii\base\Widget;


class CardsDiscounts extends Widget
{
    public $dataprovider;
    public $settings;

    public function run()
    {
        return $this->render($this->settings['view'] ?? 'index', [
            'dataProvider' => $this->dataprovider,
            'settings' => $this->settings,
        ]);
    }
}