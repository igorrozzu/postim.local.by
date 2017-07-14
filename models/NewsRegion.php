<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_news_region".
 *
 * @property integer $id
 * @property string $url_name
 * @property string $name
 * @property integer $region_id
 */
class NewsRegion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_news_region';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url_name', 'name', 'region_id'], 'required'],
            [['region_id'], 'integer'],
            [['url_name', 'name'], 'string', 'max' => 100],
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
            'name' => 'Name',
            'region_id' => 'Region ID',
        ];
    }
}
