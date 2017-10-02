<?php

namespace app\models\entities;

use app\models\Posts;
use app\models\User;
use Yii;

/**
 * This is the model class for table "tbl_gallery".
 *
 * @property integer $id
 * @property integer $post_id
 * @property integer $user_id
 * @property string $link
 * @property integer $user_status
 * @property integer $status
 *
 * @property Posts $post
 * @property User $user
 */
class Gallery extends \yii\db\ActiveRecord
{
    const USER_STATUS = ['owner' => 1, 'user' => 0];
    const PHOTO_FOLDER = '/post_photo/';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_gallery';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['post_id', 'user_id', 'link', 'user_status'], 'required'],
            [['post_id', 'user_id', 'user_status', 'status'], 'integer'],
            [['link'], 'string'],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => Posts::className(), 'targetAttribute' => ['post_id' => 'id']],
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
            'post_id' => 'Post ID',
            'user_id' => 'User ID',
            'link' => 'Link',
            'user_status' => 'User Status',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(Posts::className(), ['id' => 'post_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getPhotoPath()
    {
        return self::PHOTO_FOLDER . $this->post_id . DIRECTORY_SEPARATOR . $this->link;
    }

    public function getUserPhoto()
    {
        return self::PHOTO_FOLDER . $this->user_id . DIRECTORY_SEPARATOR . 'pho';
    }

    public static function getProfilePhotoCount(int $userId): int
    {
        return static::find()
			->joinWith(['post'=>function($query){
				$query->select('status, id, ');
			}])
			->where(['tbl_gallery.user_id' => $userId,'tbl_posts.status'=>1])
            ->count();
    }

    public static function getPostPhotoCount(int $postId): int
    {
        return static::find()
            ->where(['post_id' => $postId])
            ->count();
    }

    public static function getPreviewProfilePhoto(int $userId, int $limit): array
    {
        return static::find()
            ->where(['user_id' => $userId])
            ->orderBy(['id' => SORT_DESC])
            ->limit($limit)
            ->all();
    }

    public static function getPreviewPostPhoto(int $postId, int $limit): array
    {
        return static::find()
            ->where(['post_id' => $postId])
            ->orderBy(['user_status' => SORT_DESC, 'id' => SORT_DESC])
            ->limit($limit)
            ->all();
    }

    public static function getProfilePreviewPhoto(int $userId, int $limit): array
    {
        return static::find()
			->joinWith(['post'=>function($query){
				$query->select('status, id, ');
			}])
            ->where(['tbl_gallery.user_id' => $userId,'tbl_posts.status'=>1])

            ->orderBy(['id' => SORT_DESC])
            ->limit($limit)
            ->all();
    }

}
