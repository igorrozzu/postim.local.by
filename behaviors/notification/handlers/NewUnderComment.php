<?php

namespace app\behaviors\notification\handlers;

use app\models\Comments;
use app\models\entities\Task;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class NewUnderComment extends Behavior
{
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'run',
        ];
    }

    public function run()
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->owner->isRelatedWithNews()) {
            return $this->handleNewsUnderComment();
        } else {
            if ($this->owner->isRelatedWithReviews()) {
                return $this->handleReviewUnderComment();
            }
        }

        return false;
    }

    public function handleNewsUnderComment()
    {
        $task = new Task([
            'data' => json_encode([
                'class' => 'NewsUnderComment',
                'params' => [
                    'receiver_comment_id' => $this->owner->receiver_comment_id,
                    'user_id' => $this->owner->user_id,
                ],
            ]),
            'type' => Task::TYPE['notification'],
        ]);

        return $task->save();
    }

    public function handleReviewUnderComment()
    {
        $task = new Task([
            'data' => json_encode([
                'class' => 'ReviewUnderComment',
                'params' => [
                    'receiver_comment_id' => $this->owner->receiver_comment_id,
                    'user_id' => $this->owner->user_id,
                ],
            ]),
            'type' => Task::TYPE['notification'],
        ]);

        return $task->save();
    }

    private function isValid()
    {
        return $this->owner->getScenario() === Comments::$ADD_UNDER_COMMENT;
    }
}