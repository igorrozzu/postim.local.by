<?php

namespace app\models;

use app\behaviors\notification\handlers\FillingProfile;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "tbl_users_info".
 *
 * @property integer $user_id
 * @property integer $level
 * @property integer $exp_points
 * @property double $virtual_money
 * @property integer $total_comments
 * @property integer $count_places_added
 * @property integer $count_place_moderation
 * @property integer $gender
 * @property integer $email_alert_subscription
 * @property integer $answers_to_reviews_sub
 * @property integer $answers_to_comments_sub
 * @property integer $reviews_and_comments_to_places_sub
 * @property integer $places_and_discounts_sub
 * @property integer $has_reward_for_filling_profile
 */
class UserInfo extends \yii\db\ActiveRecord
{
    const NOT_SELECTED_GENDER_VALUE = 0;
    const MAN_GENDER_VALUE = 1;
    const WOMAN_GENDER_VALUE = 2;
    const ALLOW_GENDER_VALUES = [self::MAN_GENDER_VALUE, self::WOMAN_GENDER_VALUE];
    const ALLOW_USER_CHOICE = ['no' => 0, 'yes' => 1];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_users_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['user_id', 'required'],
            [['user_id', 'level', 'exp_points', 'total_comments', 'count_places_added', 'count_place_moderation',
                'gender', 'email_alert_subscription', 'answers_to_reviews_sub', 'answers_to_comments_sub',
                'reviews_and_comments_to_places_sub', 'places_and_discounts_sub', 'has_reward_for_filling_profile'], 'integer'],
            [['virtual_money'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'level' => 'Level',
            'exp_points' => 'Exp Points',
            'virtual_money' => 'Virtual Money',
            'total_comments' => 'Total Comments',
            'count_places_added' => 'Count Places Added',
            'count_place_moderation' => 'Count Place Moderation',
            'gender' => 'Gender',
            'email_alert_subscription' => 'Email Alert Subscription',
            'answers_to_reviews_sub' => 'Answers To Reviews Sub',
            'answers_to_comments_sub' => 'Answers To Comments Sub',
            'reviews_and_comments_to_places_sub' => 'Reviews And Comments To Places Sub',
            'places_and_discounts_sub' => 'Places And Discounts Sub',
        ];
    }

    public function behaviors()
    {
        return [
            'notification' => [
                'class' => 'app\behaviors\notification\Notification',
                'handlers' => [
                    'afterUpdate' => FillingProfile::className()
                ],
                'params' => [
                    'afterUpdate' => ['exp' => 100, 'money' => 1, 'template' => 'reward.profile'],
                ],
            ],
        ];
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function getUserGender($gender)
    {
        switch ((int)$gender) {
            case self::MAN_GENDER_VALUE: return 'Мужской'; break;
            case self::WOMAN_GENDER_VALUE: return 'Женский'; break;
        }

        return null;
    }

    public static function getUserChoice($choice)
    {
        switch ((int)$choice) {
            case self::ALLOW_USER_CHOICE['no']: return 'Нет'; break;
            case self::ALLOW_USER_CHOICE['yes']: return 'Да'; break;
        }

        return null;
    }

    public function isGenderDefined(): bool
    {
        return in_array($this->gender, self::ALLOW_GENDER_VALUES, true);
    }

    public function isGenderNotSelected(): bool
    {
        return $this->gender === self::NOT_SELECTED_GENDER_VALUE;
    }

    public function isUserMan(): bool
    {
        return $this->gender === self::MAN_GENDER_VALUE;
    }

    public function isUserWoman(): bool
    {
        return $this->gender === self::WOMAN_GENDER_VALUE;
    }

    public function hasRewardForFillingProfile(): bool
    {
        return (bool) $this->has_reward_for_filling_profile;
    }

    /**
     * Formula: d*x^2 + (2*a1 - d)*x - 2*exp = 0
     *
     * @return int
     */
    public function getLevelByExperience(): int
    {
        if ($this->exp_points === 0) {
            return 0;
        }

        $increment = Yii::$app->params['user.incrementExperience'];
        $a = $increment;
        $b = 2 * Yii::$app->params['user.experienceForFirstLevel'] - $increment;
        $c = -2 * $this->exp_points;

        $d = (pow($b,2)) - (4 * $a * $c);

        if ($d <= 0) {
            $d = (-1) * $d;
        }

        $x1 = (-2 * $c) / ($b + (sqrt($d)));
        $x2 = (-2 * $c) / ($b - (sqrt($d)));
        $maxRoot = $x1 > $x2 ? $x1 : $x2;

        return (int) abs($maxRoot);
    }

    /**
     * Formula: d*x^2 + (2*a1 - d)*x - 2*exp = 0
     * Experience: (d*x + (2*a1 - d)) * x / 2
     *
     * @param int $level
     * @return int
     */
    public function getMinExperienceByLevel(int $level): int
    {
        if ($level === 0) {
            return 0;
        }

        $increment = Yii::$app->params['user.incrementExperience'];
        return (int) ($increment * $level + (2 * Yii::$app->params['user.experienceForFirstLevel'] -
                    $increment)) * $level / 2;
    }

    /**
     * @return \stdClass
     */
    public function getExperienceInfo(): \stdClass
    {
        $expForNextLevel = $this->getMinExperienceByLevel($this->level + 1);

        $result = new \stdClass();
        $result->persent = (int) ($this->exp_points / $expForNextLevel * 100);
        $result->needExpForNextLevel = $expForNextLevel - $this->exp_points;

        return $result;
    }
}
