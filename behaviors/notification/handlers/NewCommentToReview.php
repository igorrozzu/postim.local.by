<?php

namespace app\behaviors\notification\handlers;

use app\models\entities\Task;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class NewCommentToReview extends Behavior
{
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'run'
        ];
    }

    public function run()
    {
        if (!$this->owner->isRelatedWithReviews()) {
            return false;
        }

        $task = new Task([
            'data' => json_encode([
                'class' => 'NewCommentToReview',
                'params' => [
                    'entity_id' => $this->owner->entity_id,
                    'user_id' => $this->owner->user_id,
                ],
            ]),
            'type' => Task::TYPE['notification'],
        ]);

        return $task->save();
    }
}