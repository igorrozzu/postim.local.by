<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_features".
 *
 * @property string $id
 * @property string $name
 * @property integer $type
 * @property integer $filter_status
 * @property string $main_features
 *
 * @property CategoryFeatures[] $tblCategoryFeatures
 * @property PostFeatures[] $tblPostFeatures
 * @property UnderCategoryFeatures[] $tblUnderCategoryFeatures
 */
class Features extends \yii\db\ActiveRecord
{
    public $underFeatures=null;
    public $max = null;
    public $min = null;
    public $value = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_features';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name', 'filter_status'], 'required'],
            [['type', 'filter_status'], 'integer'],
            [['id', 'name', 'main_features'], 'string', 'max' => 100],
            [['id'], 'unique'],
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
            'type' => 'Type',
            'filter_status' => 'Filter Status',
            'main_features' => 'Main Features',
        ];
    }

    public function afterFind()
    {
        parent::afterFind();
        if ($this->type == 3 && $this->main_features == null) {
            $this->underFeatures = self::find()
                ->where(['filter_status' => 1])
                ->andWhere(['main_features' => $this->id])
                ->orderBy('name')
                ->all();
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryFeatures()
    {
        return $this->hasMany(CategoryFeatures::className(), ['features_id' => 'id']);
    }

    public function getMainFeatures(){
        return $this->hasOne(self::className(),['id'=>'main_features']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPostFeatures()
    {
        return $this->hasMany(PostFeatures::className(), ['features_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnderCategoryFeatures()
    {
        return $this->hasMany(UnderCategoryFeatures::className(), ['features_id' => 'id']);
    }

    public function setMinMax($category = false,$under_category = false){
        $city_url_name = Yii::$app->city->getSelected_city()['url_name'];
        if(!$min_max = Yii::$app->cache->get([$category,$under_category,$this->id,$city_url_name.'_city'])){
            $query = (new \yii\db\Query())
                ->select('MIN(value) as min , MAX(value) as max')
                ->from(PostFeatures::tableName())
                ->innerJoin(Posts::tableName(),Posts::tableName().'.id = '.PostFeatures::tableName().'.post_id')
                ->innerJoin(PostUnderCategory::tableName(),
                    PostFeatures::tableName().'.post_id = '.PostUnderCategory::tableName().'.post_id')
                ->innerJoin(UnderCategory::tableName(),
                    UnderCategory::tableName().'.id = '.PostUnderCategory::tableName().'.under_category_id')
                ->where(['features_id' => $this->id]);


            if($category){
                $query ->innerJoin(Category::tableName(),Category::tableName().'.id = '.UnderCategory::tableName().'.category_id')
                    ->andWhere([Category::tableName().'.url_name'=>$category]);
            }else{
                $query->andWhere([UnderCategory::tableName().'.url_name'=>$under_category]);
            }

            if($city_url_name){
                $query->innerJoin(City::tableName(),City::tableName().'.id = '.Posts::tableName().'.city_id')
                    ->innerJoin(Region::tableName(),Region::tableName().'.id = '.City::tableName().'.region_id')
                    ->andWhere(['or',
                        ['tbl_region.url_name'=>$city_url_name],
                        ['tbl_city.url_name'=>$city_url_name]
                    ]);
            }

            $min_max =  $query->one();
            Yii::$app->cache->set([$category,$under_category,$this->id,$city_url_name.'_city'],$min_max);
        }

        if($min_max){
            $this->min = $min_max['min'];
            $this->max = $min_max['max'];
        }


    }
}
