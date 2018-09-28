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

class UploadPhotos extends Model
{
    /**
     * @var UploadedFile[]
     */
    public $files;
    public $directory;

    private $savedFiles = [];


    public function rules()
    {
        return [
            [['files'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, gif', 'maxFiles' => 10],
            [['files'], 'file', 'maxSize' => 15728640, 'maxFiles' => 10],
        ];
    }

    public function upload()
    {
        try {
            if ($this->validate()) {

                $full_path = Yii::getAlias('@webroot' . $this->directory);

                if (!is_dir($full_path)) {
                    FileHelper::createDirectory($full_path);
                }
                $rows = [];
                foreach ($this->files as $file) {
                    $photoName = Yii::$app->security->generateRandomString(8) . time() . '.png';

                    if ($file->saveAs($full_path . $photoName)) {
                        $rows[] = [
                            'link' => $this->directory . $photoName,
                        ];
                    }
                }
                $this->savedFiles = $rows;

            } else {
                return false;
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getSavedFiles()
    {
        return $this->savedFiles;
    }

}