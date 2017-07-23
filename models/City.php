<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_city".
 *
 * @property integer $id
 * @property integer $region_id
 * @property string $name
 * @property string $url_name
 */
class City extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_city';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['region_id', 'name'], 'required'],
            [['region_id'], 'integer'],
            [['name'], 'string'],
            [['url_name'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'region_id' => 'Region ID',
            'name' => 'Name',
            'url_name' => 'Url Name',
        ];
    }

    public function getRegion(){
        return $this->hasOne(Region::className(),['id'=>'region_id']);
    }

    public function getNewsCity(){
        return $this->hasOne(NewsCity::className(),['city_id'=>'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['city_id' =>'id']);
    }

    public static function removeCityById(array &$cities, int $id)
    {
        foreach ($cities as $key => &$city) {
            if($city['id'] === $id) {
                $name = $city['name'];
                unset($cities[$key]);
                return $name;
            }
        }
        return null;
    }
}
