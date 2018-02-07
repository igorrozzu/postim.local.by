<?php

namespace app\services\account;

use app\repositories\AccountHistoryRepository;
use app\repositories\UserinfoRepository;
use Yii;

class AccountService
{
    /**
     * @var UserinfoRepository
     */
    protected $_userinfoRepository;
    /**
     * @var AccountHistoryRepository
     */
    protected $_accountHistoryRepository;

    /**
     * AccountService constructor.
     * @param UserinfoRepository $userinfoRepository
     * @param AccountHistoryRepository $accountHistoryRepository
     */
    public function __construct(UserinfoRepository $userinfoRepository, AccountHistoryRepository $accountHistoryRepository)
    {
        $this->_userinfoRepository   = $userinfoRepository;
        $this->_accountHistoryRepository = $accountHistoryRepository;
    }

    public function changeAccountWithTransaction(int $userId, float $changing, string $message): bool
    {
        $transaction = Yii::$app->db->beginTransaction();
        $result = $this->changeAccount($userId, $changing, $message);

        if ($result) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return false;
        }
    }

    public function changeAccount(int $userId, float $changing, string $message): bool
    {
        $result = $this->_userinfoRepository->updateAccount($userId, $changing);
        return $result && $this->_accountHistoryRepository::add($userId, $changing, $message);
    }
}