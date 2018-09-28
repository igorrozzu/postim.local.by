<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_official_answer".
 *
 * @property integer $comments_id
 * @property integer $user_id
 *
 * @property Comments $comments
 * @property User $user
 */
class OfficialAnswer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_official_answer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comment_id', 'user_id', 'entity_id'], 'required'],
            [['comment_id', 'user_id'], 'integer'],
            [
                ['comment_id', 'user_id'],
                'unique',
                'targetAttribute' => ['comment_id', 'user_id'],
                'message' => 'The combination of Comments ID and User ID has already been taken.',
            ],
            [
                ['comment_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Comments::className(),
                'targetAttribute' => ['comment_id' => 'id'],
            ],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => ['user_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'comment_id' => 'Comments ID',
            'user_id' => 'User ID',
            'entity_id' => 'Entity ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasOne(Comments::className(), ['id' => 'comment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
