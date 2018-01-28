<?php
namespace app\repositories;

use app\models\UserInfo;

class UserinfoRepository extends BaseRepository
{
    /**
     * UserinfoRepository constructor.
     * @param UserInfo $model
     */
    public function __construct(UserInfo $model)
    {
        $this->_model = $model;
    }

    public function updateAccount(int $id, float $changing)
    {
        $userInfo = $this->_model::findOne(['user_id' => $id]);

        if (!$userInfo) {
            return false;
        }

        return $userInfo->updateCounters([
            'virtual_money' => $changing,
        ]);
    }
}