<?php

namespace app\widgets\accountStatistic;

use yii\base\Widget;


class AccountStatistic extends Widget
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