<?php

namespace app\models\entities;

use app\models\Notification;
use app\models\User;
use Yii;
use yii\db\Exception;

/**
 * This is the model class for table "tbl_notification_user".
 *
 * @property integer $notification_id
 * @property integer $user_id
 * @property integer $is_showed
 *
 * @property Notification $notification
 * @property User $user
 */
class NotificationUser extends \yii\db\ActiveRecord
{
    const SHOWED = ['no' => 0, 'yes' => 1];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_notification_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['notification_id', 'user_id'], 'required'],
            [['notification_id', 'user_id', 'is_showed'], 'integer'],
            [
                ['notification_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Notification::className(),
                'targetAttribute' => ['notification_id' => 'id'],
            ],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => ['user_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'notification_id' => 'Notification ID',
            'user_id' => 'User ID',
            'is_showed' => 'Is Showed',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotification()
    {
        return $this->hasOne(Notification::className(), ['id' => 'notification_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function markAsShowed(): int
    {
        try {
            return NotificationUser::updateAll(['is_showed' => self::SHOWED['yes']], [
                'user_id' => Yii::$app->user->id,
                'is_showed' => self::SHOWED['no'],
            ]);
        } catch (Exception $e) {
            return -1;
        }
    }

    public static function getCountNotifications(): int
    {
        return NotificationUser::find()
            ->where([
                'user_id' => Yii::$app->user->id,
                'is_showed' => self::SHOWED['no'],
            ])->count();
    }

    public function isShowed()
    {
        return (bool)$this->is_showed;
    }
}
