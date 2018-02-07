<?php
namespace app\repositories;

use app\models\entities\AccountHistory;

class AccountHistoryRepository extends AccountHistory
{
    public static function add(int $userId, float $changing, string $message)
    {
        $model = new AccountHistory([
            'user_id' => $userId,
            'changing' => $changing,
            'message' => $message,
            'type' => self::TYPE['virtualMoney'],
            'date' => time(),
        ]);

        return $model->save();
    }
}