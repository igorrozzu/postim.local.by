<?php

namespace app\widgets\photoSlider;

use yii\base\Widget;

class PhotoSlider extends Widget
{
    public $settings;

    public function run()
    {
        return $this->render('index', [
            'settings' => $this->settings,
        ]);
    }
}