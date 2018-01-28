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

        $this->accountService = $containerDI->get(AccountService::class);
        $this->accountService->changeAccount($this->params->user_id, $this->params->changing,
            "На счет поступило {$this->params->changing} рублей");
    }
}