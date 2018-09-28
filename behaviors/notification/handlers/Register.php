<?php

namespace app\behaviors\notification\handlers;

use app\components\user\ExperienceCalc;
use app\models\User;
use Yii;
use yii\db\ActiveRecord;

class Register extends NotificationHandler
{
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'run',
        ];
    }

    public function run()
    {
        $user = User::find()
            ->select(['id', 'name', 'email'])
            ->where(['id' => $this->owner->getUserId()])
            ->one();

        $template = Yii::$app->params['notificationTemplates']['reward.register'];

        $oldLevel = $this->owner->level ?? 0;
        $newLevel = ExperienceCalc::getLevelByExperience($this->owner->exp_points + $template['exp']);

        $updateResult = $this->owner->updateCounters([
            'exp_points' => $template['exp'],
            'mega_money' => $template['money'],
            'level' => $newLevel - $oldLevel,
        ]);

        $message = sprintf($template['text'], Yii::$app->params['site.hostName'] . '/bonus', $template['exp'],
            $template['money']);
        if ($updateResult) {
            parent::sendNotification($this->owner->getUserId(), [
                'type' => '',
                'data' => $message,
            ]);

            if ($oldLevel !== $newLevel) {
                parent::sendNotification($this->owner->getUserId(), [
                    'type' => '',
                    'data' => sprintf(
                        Yii::$app->params['notificationTemplates']['common.newUserLevel'],
                        $newLevel
                    ),
                ]);
            }

            return $this->mailer->compose(['html' => 'reward'], [
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