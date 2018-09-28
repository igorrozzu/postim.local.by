<?php

namespace app\commands\cron\taskFactory;

use app\commands\cron\tasks\AccountReplenishment;
use app\models\entities\Task;
use yii\mail\MailerInterface;

class TaskFactory extends TaskFactoryMethod
{
    protected function createTask(Task $task, MailerInterface $mailer)
    {
        switch ($task->type) {
            case (Task::TYPE['notification']):
                $class = 'app\commands\cron\notifications\\' . $task->data->class;
                return new $class($mailer);

            case (Task::TYPE['accountReplenishment']):
                return new AccountReplenishment();

            default:
                throw new \InvalidArgumentException("is not a valid task type");
        }
    }
}