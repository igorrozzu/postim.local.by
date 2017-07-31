<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_countries".
 *
 * @property integer $id
 * @property string $name
 * @property string $url_name
 */
class Countries extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_countries';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'url_name'], 'string', 'max' => 30],
            [['name'], 'unique'],
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
}
