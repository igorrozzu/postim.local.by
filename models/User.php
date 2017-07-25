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
 * @property string $password_reset_token
 */
class User extends ActiveRecord implements IdentityInterface
{
    const CITY_NOT_DEFINED = -1;
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
            [['name', 'surname'], 'string', 'max' => 25],
            [['email', 'password'], 'string'],
            [['role', 'city_id', 'has_social_creation', 'has_changing_password', 'photo_hash'], 'integer'],
            [['password_reset_token'], 'string', 'max' => 100],
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
            'password_reset_token' => 'Password Reset Token',
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
        return static::findIdentity($token);
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
        return $this->getId();
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($id)
    {
        return $this->getAuthKey() === $id;
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
        $this->password_reset_token = Yii::$app->security->generateRandomString($length) . $this->getId();
    }


    public function resetPassword($password)
    {
        $this->setPassword($password);
        $this->has_changing_password = 1;
        return $this->resetPasswordToken();
    }

    public function resetPasswordToken()
    {
        $this->password_reset_token = null;
        return $this->save();
    }

    public function getPhoto()
    {
        $userPhotoDir = '/user_photo/' . $this->id;
        if (is_dir(Yii::getAlias('@webroot' . $userPhotoDir))) {
            return $userPhotoDir . '/' . Yii::$app->params['user.photoName'] . '?' . $this->photo_hash;
        } else {
            return '/img/default-profile-icon.png';
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSocialBindings()
    {
        return $this->hasMany(SocialAuth::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserInfo()
    {
        return $this->hasOne(UserInfo::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' =>'city_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotificationToMe()
    {
        return $this->hasMany(Notification::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotificationFromMe()
    {
        return $this->hasMany(Notification::className(), ['sender_id' => 'id']);
    }

    public function isCityDefined()
    {
        return $this->city_id > 0 && $this->city_id !== self::CITY_NOT_DEFINED;
    }

    public function hasSocialCreation()
    {
        return (bool)$this->has_social_creation;
    }

    public function hasChangingPassword()
    {
        return (bool)$this->has_changing_password;
    }

    public function changeEmail(string $email)
    {
        $this->email = $email;
        return $this->save();
    }

    public function trimNames(int $length)
    {
        $this->name = mb_substr($this->name, 0, $length);
        $this->surname = mb_substr($this->surname, 0, $length);
    }
}
