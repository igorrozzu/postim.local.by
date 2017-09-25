<?php

namespace app\behaviors\notification;

use \yii\base\Behavior;
use yii\db\ActiveRecord;

class Notification extends Behavior
{
    public $handlers;
    public $params = [];

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'handleEvent',
            ActiveRecord::EVENT_AFTER_UPDATE => 'handleEvent'
        ];
    }

    public function handleEvent($event)
    {
        $e = $event->name;
        if (isset($this->handlers[$e])) {
            $handler = new $this->handlers[$e]($this->owner, $this->params[$e] ?? []);
            $handler->run();
        }
    }
}