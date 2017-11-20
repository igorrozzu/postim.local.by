<?php

namespace app\behaviors\notification\handlers;

use app\components\user\ExperienceCalc;
use app\models\User;
use Yii;
use yii\db\ActiveRecord;

class FillingProfile extends NotificationHandler
{
    const MAX_SOCIAL_BINDING = 1;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_UPDATE => 'run'
        ];
    }

    public function run()
    {
        $user = User::find()
            ->joinWith(['userInfo', 'socialBindings'])
            ->where([User::tableName() . '.id' => $this->owner->getUserId()])
            ->one();

        if ($user === null || !$this->validateFillingData($user)) {
            return false;
        }

        $template = Yii::$app->params['notificationTemplates']['reward.profile'];

        $oldLevel = $user->userInfo->level;
        $newLevel = ExperienceCalc::getLevelByExperience($user->userInfo->exp_points + $template['exp']);

        $updateResult = $user->userInfo->updateCounters([
            'exp_points' => $template['exp'],
            'mega_money' => $template['money'],
            'level' => $newLevel - $oldLevel,
            'has_reward_for_filling_profile' => 1,
        ]);

        $message = sprintf(Yii::$app->params['site.hostName'].'/bonus',$template['text'], $template['exp'], $template['money']);
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

            if (!$user->userInfo->hasExperienceAndBonusSub()) {
                return true;
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

    private function validateFillingData(ActiveRecord $user)
    {
        return !$user->userInfo->hasRewardForFillingProfile() && $user->name !== '' &&
            $user->surname !== '' && $user->isCityDefined() && $user->userInfo->isGenderDefined() &&
            $user->isPhotoDefined() && count($user->socialBindings) >= self::MAX_SOCIAL_BINDING;
    }
}