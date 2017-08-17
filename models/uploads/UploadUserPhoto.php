<?php

namespace app\models\uploads;

use app\components\ImageHelper;
use app\models\User;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\imagine\Image;
use yii\web\UploadedFile;
use yii\validators\ImageValidator;

class UploadUserPhoto extends Model
{
    /**
     * @var UploadedFile
     */
    public $imageFile;

    public function rules()
    {
        return [
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, gif'],
            [['imageFile'], 'file', 'maxSize' => 5242880],
            ['imageFile', 'image', 'minWidth' => 300, 'minHeight' => 300,]
        ];
    }

    public function upload($user)
    {
        try {
            if ($this->validate()) {
                $dir = Yii::getAlias('@webroot/user_photo/' . $user->getId() . '/');
                if(!is_dir($dir)){
                    FileHelper::createDirectory($dir);
                }
                $pathToPhoto = $dir . Yii::$app->params['user.photoName'];
                if($this->imageFile->saveAs($pathToPhoto)) {
                    ImageHelper::createSquarePicture($pathToPhoto);
                    $user->photo_hash = time();
                    $user->save();
                }
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }
}