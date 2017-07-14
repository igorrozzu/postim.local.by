<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tbl_social_auth".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $source
 * @property string $source_id
 *
 * @property User $user
 */
class SocialAuth extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_social_auth';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'source', 'source_id'], 'required'],
            [['user_id'], 'integer'],
            [['source', 'source_id'], 'string', 'max' => 30],
            ['screen_name', 'string', 'max' => 50],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'source' => 'Source',
            'source_id' => 'Source ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }


    public static function findBySource(array &$bindings, $source)
    {
        foreach ($bindings as &$binding) {
            if($binding->source === $source) {
                return $binding;
            }
        }
        return null;
    }

    public function createSocialUrl(): string
    {
        switch ($this->source) {
            case 'twitter': return 'https://twitter.com/' . $this->screen_name; break;
            case 'vkontakte': return 'https://vk.com/id' . $this->source_id; break;
            case 'odnoklassniki': return 'https://www.ok.ru/profile/' . $this->source_id; break;
            case 'facebook': return 'https://www.facebook.com/' . $this->source_id; break;
            case 'google': return 'https://plus.google.com/' . $this->source_id; break;
        }
    }
}
