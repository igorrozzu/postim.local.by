<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_reviews_complaint".
 *
 * @property integer $reviews_id
 * @property integer $user_id
 * @property string $message
 *
 * @property Reviews $reviews
 * @property Users $user
 */
class ReviewsComplaint extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_reviews_complaint';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['reviews_id', 'user_id'], 'required'],
            [['message'], 'required','message'=>'Опишите суть жалобы'],
            [['reviews_id', 'user_id'], 'integer'],
            [['message'], 'string'],
            [['reviews_id'], 'exist', 'skipOnError' => true, 'targetClass' => Reviews::className(), 'targetAttribute' => ['reviews_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'reviews_id' => 'Reviews ID',
            'user_id' => 'User ID',
            'message' => 'Message',
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
