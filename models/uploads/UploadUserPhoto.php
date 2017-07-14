<?php

namespace app\models\uploads;

use Yii;
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
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, gif',
                'message' => 'Допустимы типы jpeg, png и gif'],
            [['imageFile'], 'file', 'maxSize' => 10485760,
                'message' => 'Размер фото не более 10485760 байт'],
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $id = Yii::$app->user->getId();
            $dir = Yii::getAlias('@webroot/user_photo/' . $id . '/');
            if(!is_dir($dir)){
                FileHelper::createDirectory($dir);
            }
            $pathToPhoto = $dir . Yii::$app->params['user.photoName'];
            if($this->imageFile->saveAs($pathToPhoto)) {
                $info = getimagesize($pathToPhoto);
                $minSide = ($info[0] <= $info[1])? $info[0] : $info[1];
                Image::crop($pathToPhoto, $minSide, $minSide)
                    ->save($pathToPhoto, ['quality' => 80]);
            }
            return true;
        } else {
            return false;
        }
    }
}