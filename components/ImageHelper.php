<?php
/**
 * Created by PhpStorm.
 * User: igorrozu
 * Date: 7/10/17
 * Time: 5:14 PM
 */

namespace app\components;


use app\models\User;
use Yii;
use yii\helpers\FileHelper;
use yii\imagine\Image;

class ImageHelper
{
    public static function saveUserPhoto(string $fromUrl, User $user): bool
    {
        if($fromUrl === null) {
            return false;
        }
        $name = Yii::$app->params['user.photoName'];
        try{
            $file = file_get_contents($fromUrl);
            $dir = Yii::getAlias('@webroot/user_photo/' . $user->getId() . '/');
            if(!is_dir($dir)){
                FileHelper::createDirectory($dir);
            }
            if(file_put_contents($dir . $name, $file)) {
                static::createSquarePicture($dir . $name);
                return true;
            }

        } catch(\Exception $e){
            return false;
        }
        return false;
    }

    public static function createSquarePicture(string $from, string $to = null, int $quality = 80): bool
    {
        if($to === null) {
            $to = $from;
        }
        try{
            $info = getimagesize($from);
            $minSide = ($info[0] <= $info[1])? $info[0] : $info[1];
            Image::crop($from, $minSide, $minSide)
                ->save($to, ['quality' => $quality]);
            return true;

        } catch(\Exception $e){
            return false;
        }
    }

}