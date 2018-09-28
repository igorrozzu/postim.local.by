<?php

namespace app\models;

use app\models\entities\Gallery;

/**
 * This is the model class for table "tbl_reviews_gallery".
 *
 * @property integer $gallery_id
 * @property integer $review_id
 *
 * @property Gallery $gallery
 * @property Reviews $review
 */
class ReviewsGallery extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_reviews_gallery';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gallery_id', 'review_id'], 'required'],
            [['gallery_id', 'review_id'], 'integer'],
            [
                ['gallery_id', 'review_id'],
                'unique',
                'targetAttribute' => ['gallery_id', 'review_id'],
                'message' => 'The combination of Gallery ID and Review ID has already been taken.',
            ],
            [
                ['gallery_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Gallery::className(),
                'targetAttribute' => ['gallery_id' => 'id'],
            ],
            [
                ['review_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Reviews::className(),
                'targetAttribute' => ['review_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'gallery_id' => 'Gallery ID',
            'review_id' => 'Review ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGallery()
    {
        return $this->hasOne(Gallery::className(), ['id' => 'gallery_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReview()
    {
        return $this->hasOne(Reviews::className(), ['id' => 'review_id']);
    }
}
