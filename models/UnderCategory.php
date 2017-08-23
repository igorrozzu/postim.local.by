<?php

namespace app\models;

use Yii;
use app\models\Category;

/**
 * This is the model class for table "tbl_under_category".
 *
 * @property integer $id
 * @property string $name
 * @property integer $category_id
 * @property string $url_name
 */
class UnderCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_under_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'category_id'], 'required'],
            [['name'], 'string'],
            [['category_id'], 'integer'],
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
            'category_id' => 'Category ID',
            'url_name' => 'Url Name',
        ];
    }

    public function getCategory(){
        return $this->hasOne(Category::className(),['id'=>'category_id']);
    }

    public function getPosts()
    {
        return $this->hasMany(Posts::className(), ['under_category_id' => 'id']);
    }

    public function getCountPlace(){
        return $this->hasOne(PostCategoryCount::className(),['category_url_name'=>'url_name'])
            ->where(['city_name'=>Yii::$app->city->getSelected_city()['name']]);
    }
}
