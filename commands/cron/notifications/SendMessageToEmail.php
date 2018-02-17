<?php

namespace app\commands\cron\notifications;


use Yii;
use yii\helpers\ArrayHelper;

class SendMessageToEmail extends BaseCronNotificationHandler
{
    public function run()
    {
        $this->params = ArrayHelper::toArray($this->params);

        if (isset($this->params['htmlLayout'])) {
            $this->mailer->htmlLayout = $this->params['htmlLayout'];
        }

        $this->mailer->compose($this->params['view'], $this->params['params'])
            ->setFrom([Yii::$app->params['mail.supportEmail'] => 'Postim.by'])
            ->setTo($this->params['toEmail'])
            ->setSubject($this->params['subject'])
            ->send();
    }
}