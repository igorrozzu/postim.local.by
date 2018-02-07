<?php
/**
 * Created by PhpStorm.
 * User: igorrozu
 * Date: 1/28/18
 * Time: 5:07 PM
 */

namespace app\commands\cron\tasks;

use app\services\account\AccountService;
use yii\di\Container;

class AccountReplenishment extends BaseTask
{
    /* @var  AccountService $accountService */
    protected $accountService;

    public function run()
    {
        $containerDI = new Container();

        $this->accountService = $containerDI->get('app\services\account\AccountService');
        $changing = str_replace(",", ".", $this->params->changing);
        $this->accountService->changeAccountWithTransaction($this->params->user_id, $changing,
            "На счет поступило {$this->params->changing} рублей");
    }
}