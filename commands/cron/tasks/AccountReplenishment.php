<?php
/**
 * Created by PhpStorm.
 * User: igorrozu
 * Date: 1/28/18
 * Time: 5:07 PM
 */

namespace app\commands\cron\tasks;

class AccountReplenishment extends BaseTask
{
    public function run()
    {
        $this->putMoney();
    }

    private function putMoney()
    {
        //print_r($this->params);
    }
}