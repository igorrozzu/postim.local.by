<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_reviews_gallery".
 *
 * @property integer $gallery_id
 * @property integer $review_id
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
}
