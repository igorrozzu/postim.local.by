<?php

namespace app\behaviors\notification\handlers;

use app\models\User;
use Yii;
use yii\db\ActiveRecord;

class FillingProfile extends Reward
{
    const MAX_SOCIAL_BINDING = 1;

    public function run()
    {
        $user = User::find()
            ->innerJoinWith(['userInfo', 'socialBindings'])
            ->where([User::tableName() . '.id' => $this->model->user_id])
            ->one();

        if (!$this->validateFillingData($user)) {
            return false;
        }

        if (parent::run()) {
            $user->userInfo->has_reward_for_filling_profile = 1;
            return $user->userInfo->save();
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