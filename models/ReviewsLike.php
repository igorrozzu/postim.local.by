<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_reviews_like".
 *
 * @property integer $user_id
 * @property integer $reviews_id
 *
 * @property Reviews $reviews
 * @property Users $user
 */
class ReviewsLike extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_reviews_like';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'reviews_id'], 'required'],
            [['user_id', 'reviews_id'], 'integer'],
            [
                ['reviews_id', 'user_id'],
                'unique',
                'targetAttribute' => ['reviews_id', 'user_id'],
                'message' => 'The combination of User ID and Reviews ID has already been taken.',
            ],
            [
                ['reviews_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Reviews::className(),
                'targetAttribute' => ['reviews_id' => 'id'],
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
            'user_id' => 'User ID',
            'reviews_id' => 'Reviews ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReviews()
    {
        return $this->hasOne(Reviews::className(), ['id' => 'reviews_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
