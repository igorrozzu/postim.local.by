<?php

namespace app\models;

use Yii;
use yii\db\Exception;

/**
 * This is the model class for table "tbl_notification".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $date
 * @property string $message
 * @property integer $sender_id
 */
class Notification extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_notification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'message', 'sender_id'], 'required'],
            [['user_id', 'sender_id', 'date', 'read'], 'integer'],
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
            'date' => 'Date',
            'message' => 'Message',
            'sender_id' => 'Sender ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecipient()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSender()
    {
        return $this->hasOne(User::className(), ['id' => 'sender_id']);
    }

    public static function markAsRead(): int
    {
        try {
            return Notification::updateAll(['read' => 1], [
                'user_id' => Yii::$app->user->id,
                'read' => 0
            ]);
        } catch (Exception $e) {
            return -1;
        }
    }

    public static function getCountNotifications(): int
    {
        return Notification::find()
            ->where([
                'user_id' => Yii::$app->user->id,
                'read' => 0
            ])->count();
    }

    public function isRead()
    {
        return (bool) $this->read;
    }
}
