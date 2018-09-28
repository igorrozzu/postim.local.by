<?php

namespace app\behaviors\notification\handlers;

use app\models\entities\NotificationUser;
use app\models\Notification;
use Yii;
use yii\base\Behavior;

abstract class NotificationHandler extends Behavior
{
    protected $mailer;

    /**
     * NewReview constructor.
     */
    public function __construct()
    {
        $this->mailer = Yii::$app->getMailer();
        $this->mailer->htmlLayout = 'layouts/notification';

        parent::__construct();
    }

    public static function sendNotification(int $userId, array $data, int $senderId = null)
    {
        $notification = new Notification([
            'message' => json_encode($data),
            'sender_id' => $senderId,
            'date' => time(),
        ]);

        if ($notification->save()) {
            $notificationUser = new NotificationUser([
                'notification_id' => $notification->id,
                'user_id' => $userId,
            ]);

            return $notificationUser->save();
        }

        return false;
    }
}