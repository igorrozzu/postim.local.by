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
    public $reviewsToMyPlaces;
    public $reviewsToFavoritePlaces;
    public $experienceAndBonus;
    public $placesAndDiscounts;

    private $user;
    const SCENARIO_PASSWORD_RESET = 'password-reset';
    const SCENARIO_EMAIL_RESET = 'email-reset';

    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => [
                'name', 'surname', 'cityId', 'gender', 'email', 'oldPassword', 'newPassword', 'newPasswordRepeat',
                'answersToReviews', 'answersToComments', 'reviewsToMyPlaces', 'reviewsToFavoritePlaces',
                'experienceAndBonus', 'placesAndDiscounts'
            ],
            self::SCENARIO_PASSWORD_RESET => [
                'oldPassword', 'newPassword', 'newPasswordRepeat'
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
            [['name'], 'required', 'message' => 'Поле обязательно для заполнения'],
            [['name', 'surname'], 'string', 'max' => 25, 'tooLong' => 'Не более {max} символов'],
            [['cityId', 'gender', 'answersToReviews', 'answersToComments', 'reviewsToMyPlaces',
                'reviewsToFavoritePlaces', 'experienceAndBonus', 'placesAndDiscounts'], 'integer'],
            [['oldPassword', 'newPassword', 'newPasswordRepeat'], 'required', 'message' => 'Поле обязательно для заполнения',
                'on' => self::SCENARIO_PASSWORD_RESET],
            ['oldPassword', 'validateOldPassword', 'on' => self::SCENARIO_PASSWORD_RESET],
            ['newPasswordRepeat', 'compare', 'compareAttribute' => 'newPassword', 'message' => 'Пароли не совпадают',
                'on' => self::SCENARIO_PASSWORD_RESET],
            [['email'], 'required', 'message' => 'Поле обязательно для заполнения', 'on' => self::SCENARIO_EMAIL_RESET],
            ['email', 'email', 'message' => 'Некорректный email-адрес.', 'on' => self::SCENARIO_EMAIL_RESET],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'on' => self::SCENARIO_EMAIL_RESET,
                'message' => 'Пользователь с таким email-адресом уже существует'],
        ];

    }

    public function validateOldPassword($attribute, $params)
    {
        if (!$this->user->validatePassword($this->oldPassword)) {
            $this->addError($attribute, 'Неверный пароль');
        }
    }

    public function changePassword(): bool
    {
        $this->oldPassword = $this->oldPassword ?? '';
        $attributeNames = ['newPassword', 'newPasswordRepeat'];

        if (!$this->user->hasSocialCreation() || $this->user->hasChangingPassword()) {
            $attributeNames[] = 'oldPassword';
        }

        if ($this->oldPassword !== '' || $this->newPassword !== '' || $this->newPasswordRepeat !== '') {
            $this->scenario = self::SCENARIO_PASSWORD_RESET;
            if ($this->validate($attributeNames, false)) {
                $this->user->setPassword($this->newPassword);
                $this->user->has_changing_password = 1;

                return true;
            } else {
                return false;
            }
        }
        return true;
    }

    public function createTempEmailData()
    {
        $tempData = new TempEmail([
            'user_id' => Yii::$app->user->id,
            'hash' => Yii::$app->security->generateRandomString(50),
            'email' => $this->email,
        ]);
        return $tempData->save() ? $tempData : null;
    }

    public function saveSettings(): bool
    {
        $transaction = $this->user->getDb()->beginTransaction();
        $this->user->setAttributes([
            'name' => $this->name,
            'surname' => $this->surname,
            'city_id' => (int)$this->cityId,
        ], false);
        if ($this->user->save()) {
            $userInfo = $this->user->userInfo;
            $userInfo->setAttributes([
                'gender' => (int)$this->gender,
                'answers_to_reviews_sub' => (int)$this->answersToReviews,
                'answers_to_comments_sub' => (int)$this->answersToComments,
                'reviews_to_my_places_sub' => (int)$this->reviewsToMyPlaces,
                'reviews_to_favorite_places_sub' => (int)$this->reviewsToFavoritePlaces,
                'experience_and_bonus_sub' => (int)$this->experienceAndBonus,
                'places_and_discounts_sub' => (int)$this->placesAndDiscounts,
            ], false);
            if ($userInfo->save()) {
                $transaction->commit();
                return true;
            }
        }

        return false;
    }

    public function resetPasswordFields()
    {
        $this->oldPassword = '';
        $this->newPassword = '';
        $this->newPasswordRepeat = '';
    }

    public function isCityDefined()
    {
        if (isset($this->cityId)) {
            $city = (int)$this->cityId;
            return ($city > 0 && $city !== User::CITY_NOT_DEFINED);
        }
        return false;
    }

    public function isGenderDefined()
    {
        if (isset($this->gender)) {
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
        if (isset($this->gender)) {
            return (int)$this->gender === UserInfo::MAN_GENDER_VALUE;
        }
        return false;
    }

    public function isUserWoman()
    {
        if (isset($this->gender)) {
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
