<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_category".
 *
 * @property integer $id
 * @property string $name
 * @property string $url_name
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_category';
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

    public function getUnderCategory()
    {
        return $this->hasMany(UnderCategory::className(), ['category_id' => 'id']);
    }

    public function getUnderCategorySort()
    {
        return $this->hasMany(UnderCategory::className(), ['category_id' => 'id'])
            ->orderBy(['name' => SORT_ASC]);
    }

    public function getCountPlace()
    {
        return $this->hasOne(PostCategoryCount::className(), ['category_url_name' => 'url_name'])
            ->where(['city_name' => Yii::$app->city->getSelected_city()['name']]);
    }


}
