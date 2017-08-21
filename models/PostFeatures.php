<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_post_features".
 *
 * @property integer $post_id
 * @property string $features_id
 * @property integer $value
 *
 * @property Features $features
 * @property Posts $post
 */
class PostFeatures extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_post_features';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['post_id', 'features_id'], 'required'],
            [['post_id'], 'integer'],
            [['value'],'double'],
            [['features_id'], 'string', 'max' => 30],
            [['features_id'], 'exist', 'skipOnError' => true, 'targetClass' => Features::className(), 'targetAttribute' => ['features_id' => 'id']],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => Posts::className(), 'targetAttribute' => ['post_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'post_id' => 'Post ID',
            'features_id' => 'Features ID',
            'value' => 'Value',
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
    public function getPost()
    {
        return $this->hasOne(Posts::className(), ['id' => 'post_id']);
    }
    public function getUnderPostFeatures(){
        return $this->hasMany(self::className(), ['features_main_id' => 'features_id'])
            ->where(['post_id'=>$this->post_id]);
    }
}
