<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_region".
 *
 * @property integer $id
 * @property string $name
 * @property string $url_name
 * @property integer $countries_id
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
            [['name', 'url_name'], 'string'],
            [['countries_id'], 'integer'],
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
            'countries_id' => 'Countries ID',
        ];
    }

    public function getCoutries(){
        return $this->hasOne(Countries::className(),['id'=>'countries_id']);
    }
}
