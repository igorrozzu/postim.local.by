<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_posts".
 *
 * @property integer $id
 * @property string $url_name
 * @property integer $city_id
 * @property integer $under_category_id
 * @property string $cover
 * @property integer $rating
 * @property string $data
 */
class Posts extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_posts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url_name', 'city_id', 'under_category_id', 'cover', 'rating', 'data'], 'required'],
            [['url_name', 'cover', 'data'], 'string'],
            [['city_id', 'under_category_id', 'rating'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'url_name' => 'Url Name',
            'city_id' => 'City ID',
            'under_category_id' => 'Under Category ID',
            'cover' => 'Cover',
            'rating' => 'Rating',
            'data' => 'Data',
        ];
    }
}
