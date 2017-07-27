<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_reviews".
 *
 * @property integer $id
 * @property integer $post_id
 * @property integer $rating
 * @property integer $like
 * @property integer $user_id
 * @property string $date
 * @property string $data
 */
class Reviews extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_reviews';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['post_id', 'rating', 'like', 'user_id', 'date', 'data'], 'required'],
            [['post_id', 'rating', 'like', 'user_id'], 'integer'],
            [['date'], 'safe'],
            [['data'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'post_id' => 'Post ID',
            'rating' => 'Rating',
            'like' => 'Like',
            'user_id' => 'User ID',
            'date' => 'Date',
            'data' => 'Data',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserInfo()
    {
        return $this->hasOne(UserInfo::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(Posts::className(), ['id' => 'post_id']);
    }
}
