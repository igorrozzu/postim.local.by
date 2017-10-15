<?php

namespace app\behaviors\notification\handlers;

use app\models\User;
use Yii;
use yii\db\ActiveRecord;

class Register extends NotificationHandler
{
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'run'
        ];
    }

    public function run()
    {
        $user = User::find()
            ->select(['name', 'email'])
            ->where(['id' => $this->owner->getUserId()])
            ->one();

        $template = Yii::$app->params['notificationTemplates']['reward.register'];

        $this->owner->exp_points += $template['exp'];
        $this->owner->mega_money += $template['money'];

        $oldLevel = $this->owner->level;
        $this->owner->level = $this->owner->getLevelByExperience();

        $message = sprintf($template['text'], $template['exp'], $template['money']);
        if ($this->owner->save()) {
            parent::sendNotification($this->owner->getUserId(), [
                'type' => '',
                'data' => $message,
            ]);

            if ($oldLevel !== $this->owner->level) {
                parent::sendNotification($this->owner->getUserId(), [
                    'type' => '',
                    'data' => sprintf(
                        Yii::$app->params['notificationTemplates']['common.newUserLevel'],
                        $this->owner->level
                    ),
                ]);
            }

            $mailer = Yii::$app->getMailer();
            $mailer->htmlLayout = 'layouts/notification';
            return $mailer->compose(['html' => 'reward'], [
                'user' => $user,
                'message' => $message,
            ])->setFrom([Yii::$app->params['mail.supportEmail'] => 'Postim.by'])
                ->setTo($user->email)
                ->setSubject('Уведомление Postim.by')
                ->send();
        }

        return false;
    }
}