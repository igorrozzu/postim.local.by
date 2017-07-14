<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_comments_news".
 *
 * @property integer $id
 * @property integer $news_id
 * @property integer $user_id
 * @property integer $main_comment_id
 * @property string $date
 * @property string $data
 * @property integer $like
 *
 * @property CommentsNews $mainComment
 * @property CommentsNews[] $commentsNews
 * @property TblNews $news
 */
class CommentsNews extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_comments_news';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['news_id', 'user_id', 'date', 'data', 'like'], 'required'],
            [['news_id', 'user_id', 'main_comment_id', 'like'], 'integer'],
            [['date'], 'safe'],
            [['data'], 'string'],
            [['main_comment_id'], 'exist', 'skipOnError' => true, 'targetClass' => CommentsNews::className(), 'targetAttribute' => ['main_comment_id' => 'id']],
            [['news_id'], 'exist', 'skipOnError' => true, 'targetClass' => TblNews::className(), 'targetAttribute' => ['news_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'news_id' => 'News ID',
            'user_id' => 'User ID',
            'main_comment_id' => 'Main Comment ID',
            'date' => 'Date',
            'data' => 'Data',
            'like' => 'Like',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMainComment()
    {
        return $this->hasOne(CommentsNews::className(), ['id' => 'main_comment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnderComments()
    {
        return $this->hasMany(CommentsNews::className(), ['main_comment_id' => 'id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNews()
    {
        return $this->hasOne(News::className(), ['id' => 'news_id']);
    }
}
