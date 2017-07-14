<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_region".
 *
 * @property integer $id
 * @property string $name
 * @property string $url_name
 */
class Region extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_region';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string'],
            [['url_name'], 'string', 'max' => 40],
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
        ];
    }

    public function getNewsRegion(){
        return $this->hasOne(NewsRegion::className(),['region_id'=>'id']);
    }
}
