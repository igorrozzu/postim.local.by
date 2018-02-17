<?php
namespace app\repositories;

use app\models\entities\Task;

class TaskRepository extends Task
{
    public static function addMailTask(string $className, array $params, int $dateOfExecution = 0)
    {
        $task = new Task([
            'data' => json_encode([
                'class' => $className,
                'params' => $params,
            ]),
            'type' => Task::TYPE['notification'],
            'date_of_execution' => $dateOfExecution,
        ]);
        $task->save();
    }

    public static function addTask(array $params, int $type, int $dateOfExecution = 0)
    {
        $task = new Task([
            'data' => json_encode([
                'params' => $params,
            ]),
            'type' => $type,
            'date_of_execution' => $dateOfExecution,
        ]);
        $task->save();
    }
}