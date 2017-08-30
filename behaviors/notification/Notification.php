<?php

namespace app\behaviors\notification;

use \yii\base\Behavior;
use yii\db\ActiveRecord;

class Notification extends Behavior
{
    public $handlerName;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'run'
        ];
    }

    public function run( $event )
    {
        $handler = new $this->handlerName($this->owner);
        $handler->run();
    }
}