<?php

namespace app\behaviors\notification\handlers;

use yii\base\Object;
use yii\db\ActiveRecord;

abstract class NotificationHandler extends Object
{
    abstract public function run();

    protected $model;

    public function __construct(ActiveRecord $model)
    {
        $this->model = $model;
    }
}