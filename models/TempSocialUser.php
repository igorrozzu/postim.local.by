<?php

namespace app\models;

use app\components\social\attributes\SocialAuthAttr;
use Yii;

/**
 * This is the model class for table "tbl_temp_social_users".
 *
 * @property integer $id
 * @property string $source
 * @property string $source_id
 * @property string $screen_name
 * @property string $name
 * @property string $surname
 * @property string $email
 * @property string $url_photo
 * @property integer $is_mail_sent
 */
class TempSocialUser extends \yii\db\ActiveRecord implements SocialAuthAttr
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_temp_social_users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['source', 'source_id'], 'required'],
            [['is_mail_sent'], 'integer'],
            [['source'], 'string', 'max' => 20],
            [['source_id'], 'string', 'max' => 30],
            [['screen_name'], 'string', 'max' => 50],
            [['name', 'surname', 'email'], 'string', 'max' => 100],
            [['url_photo'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'source' => 'Source',
            'source_id' => 'Source ID',
            'screen_name' => 'Screen Name',
            'name' => 'Name',
            'surname' => 'Surname',
            'email' => 'Email',
            'url_photo' => 'Url Photo',
            'is_mail_sent' => 'Is Mail Sent',
        ];
    }

    public function getSocialId(): string
    {
        return $this->source_id;
    }

    public function getSocialName(): string
    {
        return $this->source;
    }

    public function getScreenName(): string
    {
        return $this->screen_name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getUserPhoto()
    {
        return $this->url_photo;
    }

    public function isMailSent()
    {
        return (bool)$this->is_mail_sent;
    }

    public function trimNames(int $length)
    {
        $this->name = mb_substr($this->name, 0, $length);
        $this->surname = mb_substr($this->surname, 0, $length);
    }
}
