<?php
/**
 * Created by PhpStorm.
 * User: jrborisov
 * Date: 8.7.17
 * Time: 20.30
 */

namespace app\components\cardsReviewsWidget;

use yii\base\Widget;


class CardsReviewsWidget extends Widget
{
    public $dataProvider;
    public $settings;

    public function run()
    {
        return $this->render('index', [
            'dataProvider'=> $this->dataProvider,
            'settings' => $this->settings
        ]);
    }
}