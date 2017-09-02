<?php

namespace app\models\entities;

use app\models\User;
use Yii;

/**
 * This is the model class for table "tbl_gallery_complaint".
 *
 * @property integer $id
 * @property integer $photo_id
 * @property integer $user_id
 * @property string $message
 *
 * @property Gallery $photo
 * @property User $user
 */
class GalleryComplaint extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_gallery_complaint';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['photo_id', 'user_id', 'message'], 'required', 'message' => 'Жалоба не может быть пустой'],
            [['photo_id', 'user_id'], 'integer'],
            [['message'], 'string', 'max' => 500],
            [['photo_id', 'user_id'], 'unique', 'targetAttribute' => ['photo_id', 'user_id'], 'message' => 'Ваша жалоба уже отправлена на рассмотрение'],
            [['photo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Gallery::className(), 'targetAttribute' => ['photo_id' => 'id']],
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
            'photo_id' => 'Photo ID',
            'user_id' => 'User ID',
            'message' => 'Message',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhoto()
    {
        return $this->hasOne(Gallery::className(), ['id' => 'photo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
