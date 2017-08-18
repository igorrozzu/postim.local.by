<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_under_category_features".
 *
 * @property integer $under_category_id
 * @property string $features_id
 *
 * @property Features $features
 * @property UnderCategory $underCategory
 */
class UnderCategoryFeatures extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_under_category_features';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['under_category_id', 'features_id'], 'required'],
            [['under_category_id'], 'integer'],
            [['features_id'], 'string', 'max' => 30],
            [['features_id'], 'exist', 'skipOnError' => true, 'targetClass' => Features::className(), 'targetAttribute' => ['features_id' => 'id']],
            [['under_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => UnderCategory::className(), 'targetAttribute' => ['under_category_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'under_category_id' => 'Under Category ID',
            'features_id' => 'Features ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeatures()
    {
        return $this->hasOne(Features::className(), ['id' => 'features_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnderCategory()
    {
        return $this->hasOne(UnderCategory::className(), ['id' => 'under_category_id']);
    }
}
