<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "tbl_users".
 *
 * @property integer $id
 * @property string $name
 * @property integer $role
 * @property integer $city_id
 * @property string $login
 * @property string $email
 * @property string $password
 * @property string $auth_token
 * @property string $password_reset_token
 * @property integer $confirmed
 */
class User extends ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'city_id'], 'required'],
            [['name', 'email', 'password'], 'string'],
            [['role', 'city_id', 'confirmed'], 'integer'],
            [['auth_token', 'password_reset_token'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'role' => 'Role',
            'city_id' => 'City ID',
            'email' => 'Email',
            'password' => 'Password',
            'auth_token' => 'Auth Token',
            'password_reset_token' => 'Password Reset Token',
            'confirmed' => 'Confirmed',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_token' => $token]);
    }

    public static function findByPasswordResetToken($token)
    {
        return static::findOne(['password_reset_token' => $token]);
    }


    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_token;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }
    public function generatePassword($length = 10)
    {
        $password = Yii::$app->security->generateRandomString($length);
        $this->password = Yii::$app->security->generatePasswordHash($password);
        return $password;
    }


    public function generatePasswordResetToken($length = 32)
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString($length);
    }

    public function setAuthToken($length = 32)
    {
        $this->auth_token = Yii::$app->security->generateRandomString($length);
    }

    public function confirmAccount()
    {
        $this->confirmed = 1;
        $this->auth_token = null;
        return $this->save();
    }

    public function resetPassword($password)
    {
        $this->setPassword($password);
        $this->password_reset_token = null;
        return $this->save();
    }

    public function getPhoto()
    {
        return Yii::getAlias('@webroot/user_photo/' . $this->id .
            '/' . Yii::$app->params['user.photoName'] . '?' . time());
    }

    public function isConfirmed()
    {
        return (bool)$this->confirmed;
    }


}
