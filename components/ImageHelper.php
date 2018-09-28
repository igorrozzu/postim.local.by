<?php
/**
 * Created by PhpStorm.
 * User: igorrozu
 * Date: 7/10/17
 * Time: 5:14 PM
 */

namespace app\components;


use app\models\User;
use Imagine\Gd\Imagine;
use Imagine\Image\ManipulatorInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Yii;
use yii\helpers\FileHelper;
use yii\imagine\Image;

class ImageHelper
{
    public static function saveUserPhoto(string $fromUrl, User $user): bool
    {
        if ($fromUrl === null) {
            return false;
        }
        $name = Yii::$app->params['user.photoName'];
        try {
            $file = file_get_contents($fromUrl);
            $dir = Yii::getAlias('@webroot/user_photo/' . $user->getId() . '/');
            if (!is_dir($dir)) {
                FileHelper::createDirectory($dir);
            }
            if (file_put_contents($dir . $name, $file)) {
                static::createSquarePicture($dir . $name);
                return true;
            }

        } catch (\Exception $e) {
            return false;
        }
        return false;
    }

    public static function createSquarePicture(string $from, string $to = null, int $quality = 80): bool
    {
        if ($to === null) {
            $to = $from;
        }
        try {
            $info = getimagesize($from);
            $minSide = ($info[0] <= $info[1]) ? $info[0] : $info[1];
            $offset = $info[0] - $info[1]; //width - height
            if ($offset >= 0) {
                $start = [(int)$offset / 2, 0];
            } else {
                $start = [0, (int)abs($offset / 2)];
            }
            Image::crop($from, $minSide, $minSide, $start)
                ->save($to, ['quality' => $quality]);
            return true;

        } catch (\Exception $e) {
            return false;
        }
    }

    public static function MaxImg2000(string $from, string $to = null, int $quality = 80)
    {

        if ($to === null) {
            $to = $from;
        }

        try {

            $info = getimagesize($from);

            $newWidth = $info[0];
            $newHeight = $info[1];

            if ($info[0] > 2000) {
                $dif = 2000 / $info[0];
                $newWidth = $info[0] * $dif;
                $newHeight = floor($info[1] * $dif);
            }

            $imagine = new \Imagine\Imagick\Imagine();
            $palette = new RGB();
            $color = $palette->color('#fff', 100);
            $topLeft = new Point(0, 0);

            $image = Image::thumbnail($from, $newWidth, $newHeight)
                ->strip();

            $canvas = $imagine->create($image->getSize(), $color);

            $canvas->paste($image, $topLeft)
                ->save($to, [
                        'quality' => $quality,
                    ]
                );

        } catch (\Exception $e) {
            return false;
        }

    }

}