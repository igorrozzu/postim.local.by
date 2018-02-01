<?php

namespace app\models\entities;

use Yii;

/**
 * This is the model class for table "tbl_recommended_posts".
 *
 * @property integer $id
 * @property string $key
 * @property string $queue
 * @property integer $updating_at
 */
class RecommendedPosts extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_recommended_posts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'updating_at'], 'required'],
            [['key', 'queue'], 'string'],
            [['updating_at'], 'integer'],
            [['key'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'queue' => 'Queue',
            'updating_at' => 'Updating At',
        ];
    }
}
