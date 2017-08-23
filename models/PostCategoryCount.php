<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_post_category_count".
 *
 * @property string $category_url_name
 * @property string $city_name
 * @property integer $count
 */
class PostCategoryCount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_post_category_count';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_url_name', 'city_name', 'count'], 'required'],
            [['count'], 'integer'],
            [['category_url_name', 'city_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'category_url_name' => 'Category Url Name',
            'city_name' => 'City Name',
            'count' => 'Count',
        ];
    }
}
