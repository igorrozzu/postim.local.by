<?php

namespace app\models;

use Yii;
use yii\base\Model;


class UserSettings extends Model
{
    public $name;
    public $surname;
    public $cityId;
    public $gender;

    public $email;
    public $oldPassword;
    public $newPassword;
    public $newPasswordRepeat;

    public $answersToReviews;
    public $answersToComments;
    public $reviewsAndCommentsToPlaces;
    public $placesAndDiscounts;

    private $user;
    const SCENARIO_PASSWORD_RESET = 'password-reset';
    const SCENARIO_EMAIL_RESET = 'email-reset';

    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => [
                'name', 'surname', 'cityId', 'gender', 'email', 'oldPassword', 'newPassword','newPasswordRepeat',
                'answersToReviews', 'answersToComments', 'reviewsAndCommentsToPlaces', 'placesAndDiscounts',
            ],
            self::SCENARIO_PASSWORD_RESET => [
                'oldPassword', 'newPassword','newPasswordRepeat'
            ],
            self::SCENARIO_EMAIL_RESET => [
                'email',
            ],
        ];
    }
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'email'], 'required', 'message'=> 'Поле обязательно для заполнения'],
            ['email', 'email', 'message' => 'Некорректный email-адрес.'],
            [['cityId', 'gender', 'answersToReviews', 'answersToComments', 'reviewsAndCommentsToPlaces',
                'placesAndDiscounts'], 'integer'],
            [['oldPassword', 'newPassword','newPasswordRepeat'], 'required', 'message'=> 'Поле обязательно для заполнения',
                'on' => self::SCENARIO_PASSWORD_RESET],
            ['oldPassword', 'validateOldPassword', 'on' => self::SCENARIO_PASSWORD_RESET],
            ['newPasswordRepeat', 'compare', 'compareAttribute' => 'newPassword', 'message' => 'Пароли не совпадают',
                'on' => self::SCENARIO_PASSWORD_RESET],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'on' => self::SCENARIO_EMAIL_RESET,
                'message' => 'Пользователь с таким адресом уже существует'],

        ];

    }

    public function validateOldPassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (!$this->user->validatePassword($this->oldPassword)) {
                $this->addError($attribute, 'Пользователя с таким паролем не существует');
            }
        }
    }

    public function changePassword(): bool
    {
        if($this->oldPassword !== '' || $this->newPassword !== '' || $this->newPasswordRepeat !== '') {
            $this->scenario = self::SCENARIO_PASSWORD_RESET;
            if($this->validate()) {
                $this->user->setPassword($this->newPassword);
                return true;
            }
        }
        return false;
    }

    public function changeEmail(): bool
    {
        if($this->user->email !== $this->email) {
            $this->scenario = self::SCENARIO_EMAIL_RESET;
            if(!$this->hasErrors() && $this->validate()) {
                $this->user->email = $this->email;
                $this->user->confirmed = 0;
                return true;
            }
        }
        return false;
    }
    public function saveSettings(): bool
    {
        $transaction = $this->user->getDb()->beginTransaction();
        $this->user->setAttributes([
            'name' => $this->name,
            'surname' => $this->surname,
            'city_id' => (int)$this->cityId,
        ], false);
        if($this->user->save()) {
            $userInfo = $this->user->userInfo;
            $userInfo->setAttributes([
                'gender' => (int)$this->gender,
                'answers_to_reviews_sub' => (int)$this->answersToReviews,
                'answers_to_comments_sub' => (int)$this->answersToComments,
                'reviews_and_comments_to_places_sub' => (int)$this->reviewsAndCommentsToPlaces,
                'places_and_discounts_sub' => (int)$this->placesAndDiscounts,
            ], false);
            if($userInfo->save()) {
                $transaction->commit();
                return true;
            }
        }

        return false;
    }

    public function isCityDefined()
    {
        if(isset($this->cityId)) {
            $city = (int)$this->cityId;
            return ($city > 0 && $city !== User::CITY_NOT_DEFINED);
        }
        return false;
    }

    public function isGenderDefined()
    {
        if(isset($this->gender)) {
            return in_array((int)$this->gender, UserInfo::ALLOW_GENDER_VALUES, true);
        }
        return false;
    }

    public function isGenderNotSelected()
    {
        return !isset($this->gender) ||
            ((int)$this->gender === UserInfo::NOT_SELECTED_GENDER_VALUE);
    }

    public function isUserMan()
    {
        if(isset($this->gender)) {
            return (int)$this->gender === UserInfo::MAN_GENDER_VALUE;
        }
        return false;
    }

    public function isUserWoman()
    {
        if(isset($this->gender)) {
            return (int)$this->gender === UserInfo::WOMAN_GENDER_VALUE;
        }
        return false;
    }

    /**
     * @param mixed $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }
}
