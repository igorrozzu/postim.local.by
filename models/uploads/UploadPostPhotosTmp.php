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

class UploadPostPhotosTmp extends Model
{
    /**
     * @var UploadedFile[]
     */
    public $files;

    private $savedFiles = [];

    /**
     * @var string
     */
    private $directory;

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
                $dir = Yii::getAlias($this->directory);
                if (!is_dir($dir)) {
                    FileHelper::createDirectory($dir);
                }
                $rows = [];
                foreach ($this->files as $file) {
                    $photoName = Yii::$app->security->generateRandomString(8) . time() . '.jpg';

                    if ($file->saveAs($dir . $photoName)) {

                        ImageHelper::MaxImg2000($dir . $photoName);

                        $rows[] = [
                            'link' => $photoName,
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

    /**
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * @param string $directory
     */
    public function setDirectory(string $directory)
    {
        $this->directory = $directory;
    }
}