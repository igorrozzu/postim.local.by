<?php

namespace app\behaviors\notification\handlers;

use app\models\entities\NotificationUser;
use app\models\Notification;
use yii\base\Behavior;

abstract class NotificationHandler extends Behavior
{
    public static function sendNotification(int $userId, array $data, int $senderId = null)
    {
        $notification = new Notification([
            'message' => json_encode($data),
            'sender_id' => $senderId,
            'date' => time(),
        ]);

        $transaction = $notification->getDb()->beginTransaction();

        if ($notification->save()) {
            $notificationUser = new NotificationUser([
                'notification_id' => $notification->id,
                'user_id' => $userId,
            ]);

            $result = $notificationUser->save();
            $transaction->commit();

            return $result;
        }

        return false;
    }
}