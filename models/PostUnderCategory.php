<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_post_under_category".
 *
 * @property integer $under_category_id
 * @property integer $post_id
 *
 * @property Posts $post
 * @property UnderCategory $underCategory
 */
class PostUnderCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_post_under_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['under_category_id', 'post_id'], 'required'],
            [['under_category_id', 'post_id'], 'integer'],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => Posts::className(), 'targetAttribute' => ['post_id' => 'id']],
            [['under_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => UnderCategory::className(), 'targetAttribute' => ['under_category_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'under_category_id' => 'Under Category ID',
            'post_id' => 'Post ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(Posts::className(), ['id' => 'post_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnderCategory()
    {
        return $this->hasOne(UnderCategory::className(), ['id' => 'under_category_id']);
    }
}
