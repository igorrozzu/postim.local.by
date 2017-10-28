<?php

namespace app\models\uploads;

use app\components\ImageHelper;
use app\models\entities\Gallery;
use app\models\entities\OwnerPost;
use app\models\User;
use linslin\yii2\curl\Curl;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\imagine\Image;
use yii\web\UploadedFile;
use yii\validators\ImageValidator;

class UploadPhotosByUrl extends Model
{

    public $urlToImg = null;

    private $savedFiles = [];


    public function rules()
    {
        return [
            [['urlToImg'], 'match', 'pattern' => '/.+\.(jpg)|(png)|(gif)|(jpeg)$/',
                'message' => 'Изображение должно быть в формате JPG, GIF или PNG.'],
        ];
    }


    public function upload()
    {
        try {
            if ($this->validate()) {
                $dir = '/post-img/'.time().'/';
                $full_path = Yii::getAlias('@webroot'.$dir);

                if (!is_dir($full_path)) {
                    FileHelper::createDirectory($full_path);
                }
                $rows = [];

                $photoName = Yii::$app->security->generateRandomString(8).time().'.'.pathinfo($this->urlToImg,PATHINFO_EXTENSION);

                $curl = new Curl();

                $img = $curl->get($this->urlToImg);
                file_put_contents($full_path.$photoName,$img);

                if(file_exists($full_path.$photoName)){
                    $rows[] = [
                        'link' => $dir.$photoName,
                    ];
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

    public function getSavedFiles(){
    	return $this->savedFiles;
	}

}