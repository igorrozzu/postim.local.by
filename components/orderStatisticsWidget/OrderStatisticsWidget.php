<?php
/**
 * Created by PhpStorm.
 * User: jrborisov
 * Date: 8.7.17
 * Time: 20.30
 */

namespace app\components\orderStatisticsWidget;

use yii\base\InvalidParamException;
use yii\base\Widget;


class OrderStatisticsWidget extends Widget
{
    public $dataProvider;
    public $settings;

    public function run()
    {
        if (isset($this->settings['view-name'])) {

            return $this->render($this->settings['view-name'], [
                'dataProvider' => $this->dataProvider,
                'settings' => $this->settings,
            ]);
        } else {
            throw new InvalidParamException();
        }
    }
}