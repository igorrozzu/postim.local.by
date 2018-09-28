<?php

namespace app\behaviors\notification\handlers;

use app\models\entities\Task;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class NewReview extends Behavior
{
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'run',
        ];
    }

    public function run()
    {
        $task = new Task([
            'data' => json_encode([
                'class' => 'NewReview',
                'params' => [
                    'post_id' => $this->owner->post_id,
                    'user_id' => $this->owner->user_id,
                    'id' => $this->owner->id,
                ],
            ]),
            'type' => Task::TYPE['notification'],
        ]);

        return $task->save();
    }
}