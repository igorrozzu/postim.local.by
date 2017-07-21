<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginModel is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginModel extends Model
{
    public $name;
    public $email;
    public $password;
    public $password_repeat;
    private $user = false;

    const SCENARIO_LOGIN = 'login';
    const SCENARIO_REGISTER = 'register';
    const SCENARIO_PASSWORD_RECOVERY = 'password-recovery';
    const SCENARIO_PASSWORD_RESET_FORM = 'password-reset-form';

    public function scenarios()
    {
        return [
            self::SCENARIO_LOGIN => ['email', 'password'],
            self::SCENARIO_REGISTER => ['name', 'email', 'password', 'password_repeat'],
            self::SCENARIO_PASSWORD_RECOVERY => ['email'],
            self::SCENARIO_PASSWORD_RESET_FORM => ['password', 'password_repeat'],
        ];
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name','email'], 'filter', 'filter' => 'trim'],
            ['name', 'string', 'max' => 25, 'tooLong'=> 'Не более {max} символов', 'on' => self::SCENARIO_REGISTER],
            ['email', 'required', 'message'=> 'Поле обязательно для заполнения',
                'on' => [self::SCENARIO_LOGIN, self::SCENARIO_REGISTER, self::SCENARIO_PASSWORD_RECOVERY]],
            ['email', 'email', 'message' => 'Некорректный email-адрес.',
                'on' => [self::SCENARIO_LOGIN, self::SCENARIO_REGISTER, self::SCENARIO_PASSWORD_RECOVERY]],
            ['password', 'required', 'message'=> 'Поле обязательно для заполнения',
                'on' => [self::SCENARIO_LOGIN, self::SCENARIO_REGISTER, self::SCENARIO_PASSWORD_RESET_FORM]],
            [['name', 'password_repeat'], 'required', 'on' => self::SCENARIO_REGISTER,
                'message'=> 'Поле обязательно для заполнения'],
            ['password_repeat', 'required', 'message'=> 'Поле обязательно для заполнения',
                'on' => [self::SCENARIO_PASSWORD_RESET_FORM]],
            ['password_repeat', 'compare', 'compareAttribute'=>'password', 'message' => 'Пароли не совпадают',
                'on' => [self::SCENARIO_REGISTER, self::SCENARIO_PASSWORD_RESET_FORM]],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'on' => self::SCENARIO_REGISTER,
                'message' => 'Пользователь с таким email-адресом уже существует'],
            ['password', 'validatePassword', 'on' => self::SCENARIO_LOGIN],
            ['email', 'exist', 'targetClass' => '\app\models\User', 'on' => self::SCENARIO_PASSWORD_RECOVERY,
                'message' => 'Пользователя с таким email-адресом не существует'],
        ];

    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Неверный email или пароль');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), Yii::$app->params['user.loginDuration']);
        }
        return false;
    }

    public function getUserForResetPassword()
    {
        if ($this->validate()) {
            if($user = $this->getUser()) {
                $user->generatePasswordResetToken();
                return $user->save() ? $user : null;
            }
        }
        return false;
    }

    public function createUser(TempUser $tempUser)
    {
        $user = new User([
            'name' => $tempUser->name,
            'email' => $tempUser->email,
            'password' => $tempUser->password,
            'city_id' => -1,
        ]);
        $transaction = $user->getDb()->beginTransaction();
        if($user->save()) {
            $userInfo = new UserInfo(['user_id' => $user->id]);
            if($userInfo->save()) {
                $transaction->commit();
            } else {
                return false;
            }
        } else {
            return false;
        }
        return $user;
    }

    public function createTempUser()
    {
        $user = new TempUser([
            'name' => $this->name,
            'email' => $this->email
        ]);
        $user->setPassword($this->password);
        return $user->save() ? $user : null;
    }

    /**
     * Finds user by [[email]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->user === false) {
            $this->user = User::findOne(['email' => $this->email]);
        }

        return $this->user;
    }
}
