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
    public $type_feature=false;

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
            [['id', 'name', 'main_features'], 'string', 'max' => 30],
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
        if($this->type==3 && $this->main_features==null){
            $this->underFeatures = self::find()
                ->where(['filter_status'=>1])
                ->andWhere(['main_features'=>$this->id])->all();
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryFeatures()
    {
        return $this->hasMany(CategoryFeatures::className(), ['features_id' => 'id']);
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
}
