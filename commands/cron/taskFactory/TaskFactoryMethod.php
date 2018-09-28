<?php

namespace app\commands\cron\taskFactory;

use app\models\entities\Task;
use yii\mail\MailerInterface;

abstract class TaskFactoryMethod
{
    abstract protected function createTask(Task $task, MailerInterface $mailer);

    public function create(Task $task, MailerInterface $mailer)
    {
        $task->data = json_decode($task->data);

        $obj = $this->createTask($task, $mailer);
        $obj->params = $task->data->params;

        return $obj;
    }
}