<?php

namespace app\behaviors\notification\handlers;

use yii\base\Object;
use yii\db\ActiveRecord;

abstract class NotificationHandler extends Object
{
    protected $model;
    protected $params;

    abstract public function run();

    public function __construct(ActiveRecord $model, array $config = [])
    {
        $this->model = $model;
        $this->params = $config;
    }
}