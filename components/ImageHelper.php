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
                return true;
            }

        } catch(\Exception $e){
            return false;
        }
        return false;
    }
}