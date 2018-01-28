<?php

namespace app\models\entities;

use app\models\User;
use Yii;
use yii\base\Event;

/**
 * This is the model class for table "tbl_account_history".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $changing
 * @property string $message
 * @property integer $type
 * @property integer $date
 *
 * @property User $user
 */
class AccountHistory extends \yii\db\ActiveRecord
{
    const TYPE = [
        'virtualMoney' => 1,
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_account_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'changing', 'message', 'type', 'date'], 'required'],
            [['user_id', 'type', 'date'], 'integer'],
            [['changing'], 'number'],
            [['message'], 'string'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'changing' => 'Changing',
            'message' => 'Message',
            'type' => 'Type',
            'date' => 'Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
