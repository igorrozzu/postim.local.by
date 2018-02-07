<?php
namespace app\repositories;

use app\models\UserInfo;

class UserinfoRepository extends UserInfo
{
    public function updateAccount(int $id, float $changing)
    {
        $userInfo = self::findOne(['user_id' => $id]);

        if (!$userInfo || $userInfo->virtual_money + $changing < 0) {
            return false;
        }

        return $userInfo->updateCounters([
            'virtual_money' => $changing,
        ]);
    }
}