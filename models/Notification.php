<?php

namespace app\models;

use Yii;
use yii\db\Exception;

/**
 * This is the model class for table "tbl_notification".
 *
 * @property integer $id
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
            [['message'], 'required'],
            [['sender_id', 'date'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Date',
            'message' => 'Message',
            'sender_id' => 'Sender ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSender()
    {
        return $this->hasOne(User::className(), ['id' => 'sender_id']);
    }
}
