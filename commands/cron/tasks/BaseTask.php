<?php
/**
 * Created by PhpStorm.
 * User: igorrozu
 * Date: 1/28/18
 * Time: 6:08 PM
 */

namespace app\commands\cron\tasks;


abstract class BaseTask
{
    public $params;

    abstract public function run();

}