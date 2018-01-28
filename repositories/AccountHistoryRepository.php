<?php
namespace app\repositories;

use app\models\entities\AccountHistory;

class AccountHistoryRepository extends BaseRepository
{
    /**
     * AccountHistory constructor.
     * @param AccountHistory $model
     */
    public function __construct(AccountHistory $model)
    {
        $this->_model = $model;
    }

    public function add(int $userId, float $changing, string $message)
    {
        $this->_model->setAttributes([
            'user_id' => $userId,
            'changing' => $changing,
            'message' => $message,
            'type' => $this->_model::TYPE['virtualMoney'],
            'date' => time(),
        ]);

        return $this->_model->save();
    }
}