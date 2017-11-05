<?php

namespace app\commands\cron\notifications;

use app\models\entities\NotificationUser;
use app\models\Notification;
use app\models\Posts;
use app\models\User;
use app\models\UserInfo;
use Yii;
use yii\base\Model;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

class SendMessageToEmail extends BaseCronNotificationHandler
{
    public function run()
    {
        $this->params = ArrayHelper::toArray($this->params);
        $this->mailer->compose($this->params['view'],$this->params['params'])
            ->setFrom([Yii::$app->params['mail.supportEmail'] => 'Postim.by'])
            ->setTo($this->params['toEmail'])
            ->setSubject($this->params['subject'])
            ->send();

    }

}