<?php

namespace app\models;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "tbl_post_info".
 *
 * @property string $phones
 * @property string $web_site
 * @property string $social_networks
 * @property string $editors
 * @property string $article
 * @property integer $post_id
 * @property integer $id
 *
 * @property Posts $post
 */
class PostInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_post_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['phones', 'social_networks', 'editors', 'article'], 'string'],
            [['post_id'], 'required'],
            [['post_id'], 'integer'],
            [['web_site'], 'string', 'max' => 50],
            [['post_id'], 'unique'],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => Posts::className(), 'targetAttribute' => ['post_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'phones' => 'Phones',
            'web_site' => 'Web Site',
            'social_networks' => 'Social Networks',
            'editors' => 'Editors',
            'article' => 'Article',
            'post_id' => 'Post ID',
            'id' => 'ID',
        ];
    }

    public function behaviors()
    {
        return [

            'SaveJson' => [
                'class' => 'app\behaviors\SaveJson',
                'in_attributes' => ['phones','social_networks','editors'],
            ]

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(Posts::className(), ['id' => 'post_id']);
    }

    public function afterFind()
    {
        parent::afterFind();

        if($this->phones){
            $this->phones=Json::decode($this->phones);
        }
        if($this->social_networks){
            $this->social_networks=Json::decode($this->social_networks);
        }

    }
}
