<?php

namespace app\models;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "tbl_geocoding".
 *
 * @property string $query
 * @property string $data
 */
class Geocoding extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_geocoding';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['query', 'data'], 'required'],
            [['data'], 'string'],
            [['query'], 'string', 'max' => 100],
            [['query'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'query' => 'Query',
            'data' => 'Data',
        ];
    }

    public static function buildQuery(string $cityName)
    {

        $query = 'Беларусь+' . str_replace(' ', '+', $cityName);

        return $query;

    }

    public function getBounds()
    {

        $geo = Json::decode($this->data);

        return $geo['results'][0]['geometry']['bounds'];

    }

}