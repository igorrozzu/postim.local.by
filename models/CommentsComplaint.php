<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_comments_complaint".
 *
 * @property integer $id
 * @property integer $comment_id
 * @property integer $user_id
 * @property string $message
 */
class CommentsComplaint extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_comments_complaint';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comment_id', 'user_id'], 'required'],
            [['message'], 'required','message'=>'Необходимо заполнить текст жалобы'],
            [['comment_id', 'user_id'], 'integer'],
            [['message'], 'string', 'max' => 500],
            [['comment_id'], 'exist', 'skipOnError' => true, 'targetClass' => CommentsNews::className(), 'targetAttribute' => ['comment_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'comment_id' => 'Comment ID',
            'user_id' => 'User ID',
            'message' => 'Message',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComment()
    {
        return $this->hasOne(CommentsNews::className(), ['id' => 'comment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
