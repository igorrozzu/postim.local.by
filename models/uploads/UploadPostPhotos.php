<?php

namespace app\models\uploads;

use app\components\ImageHelper;
use app\models\entities\Gallery;
use app\models\entities\OwnerPost;
use app\models\User;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\imagine\Image;
use yii\web\UploadedFile;
use yii\validators\ImageValidator;

class UploadPostPhotos extends Model
{
    /**
     * @var UploadedFile[]
     */
    public $files;
    public $postId;
    public $userStatus;

    /**
     * UploadPostPhotos constructor.
     */
    public function init()
    {
        parent::init();
        $this->userStatus = Gallery::USER_STATUS['user'];
    }

    public function rules()
    {
        return [
            [['files'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, gif', 'maxFiles' => 10],
            [['files'], 'file', 'maxSize' => 15728640, 'maxFiles' => 10],
            ['postId', 'integer'],
            ['userStatus', 'safe']
        ];
    }

    public function upload()
    {
        try {
            if ($this->validate()) {
                $dir = Yii::getAlias('@webroot/post_photo/' . $this->postId . '/');
                if (!is_dir($dir)) {
                    FileHelper::createDirectory($dir);
                }
                $headers = ['link', 'post_id', 'user_id', 'user_status','status', 'date'];
                $rows = [];
                $user = Yii::$app->user->identity;
                foreach ($this->files as $file) {
                    $photoName = Yii::$app->security->generateRandomString(8).time().'.png';

                    if ($file->saveAs($dir . $photoName)) {
                        $rows[] = [
                            'link' => $photoName,
                            'post_id' => $this->postId,
                            'user_id' => $user->id,
                            'user_status' => $this->userStatus,
                            'status' => $this->userStatus,
                            'date' => time(),
                        ];
                    }
                }
            } else {
                return false;
            }
            Yii::$app->db->createCommand()->batchInsert(Gallery::tableName(), $headers, $rows)->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function beforeValidate()
    {
        $result = OwnerPost::find()->where([
            'owner_id' => Yii::$app->user->id,
            'post_id' => (int)Yii::$app->request->post('postId'),
        ])->one();
        if (isset($result)) {
            $this->userStatus = Gallery::USER_STATUS['owner'];
        }
        return parent::beforeValidate();
    }
}