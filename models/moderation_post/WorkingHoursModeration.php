<?php

namespace app\models\moderation_post;

use Yii;

/**
 * This is the model class for table "tbl_working_hours_moderation".
 *
 * @property string $day_type
 * @property integer $time_start
 * @property integer $time_finish
 * @property integer $post_id
 * @property integer $id
 */
class WorkingHoursModeration extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_working_hours_moderation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['time_start', 'time_finish', 'post_id'], 'integer'],
            [['post_id'], 'required'],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => PostsModeration::className(), 'targetAttribute' => ['post_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'day_type' => 'Day type',
            'time_start' => 'Time Start',
            'time_finish' => 'Time Finish',
            'post_id' => 'Post ID',
            'id' => 'ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(PostsModeration::className(), ['id' => 'post_id']);
    }
}
