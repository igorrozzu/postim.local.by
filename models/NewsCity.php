<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_news_city".
 *
 * @property integer $id
 * @property string $name
 * @property string $url_name
 * @property integer $city_id
 */
class NewsCity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_news_city';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'url_name', 'city_id'], 'required'],
            [['city_id'], 'integer'],
            [['name', 'url_name'], 'string', 'max' => 40],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'url_name' => 'Url Name',
            'city_id' => 'City ID',
        ];
    }
}
