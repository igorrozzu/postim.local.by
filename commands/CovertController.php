<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\components\Helper;
use app\components\ImageHelper;
use app\models\City;
use app\models\Geocoding;
use app\models\PostInfo;
use app\models\Posts;
use linslin\yii2\curl\Curl;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;


/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class CovertController extends Controller
{


    public function actionConvertAddress()
    {

        $cities = City::find()->all();

        foreach ($cities as $city) {
            $sql = "UPDATE tbl_posts SET address = regexp_replace(address, '^{$city->name} г., ', '');";
            Yii::$app->db->createCommand($sql)->execute();
        }

    }

    public function actionConvertWebSite()
    {

        $postsInfo = PostInfo::find()->where(['<>', 'web_site', ''])->all();

        foreach ($postsInfo as $item) {

            $curl = new Curl();
            if (!$response = $curl->get($item->web_site)) {
                $item->web_site = '';
                $item->update();
            }

        }

    }


    public function actionLat()
    {

        $posts = Posts::find()->select(['id', 'coordinates'])->all();

        foreach ($posts as $post) {
            $lat = $post->lon;
            $lon = $post->lat;

            $query = "UPDATE public.tbl_posts SET coordinates = '($lat,$lon)' WHERE id={$post->id}";

            Yii::$app->db->createCommand($query)->execute();

        }

    }


    public function actionExplodeCities()
    {

        $cities = City::find()
            ->where(['NOT IN', 'id', [142]])
            //->where(['IN','id',[90,71,48,115,23,2,1]])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        foreach ($cities as $city) {

            $geo = Geocoding::find()->where(['query' => Geocoding::buildQuery($city->name)])->one();
            $bbox = $geo->getBounds();
            $northeast = $bbox['northeast'];
            $southwest = $bbox['southwest'];

            $point1 = "{$northeast['lat']} {$northeast['lng']}";
            $point2 = "{$southwest['lat']} {$northeast['lng']}";
            $point3 = "{$southwest['lat']} {$southwest['lng']}";
            $point4 = "{$northeast['lat']} {$southwest['lng']}";
            $point5 = "{$northeast['lat']} {$northeast['lng']}";


            $query = "SELECT id FROM tbl_posts WHERE city_id = 142 AND ST_Within(point,ST_GeomFromText('POLYGON(({$point1}, {$point2}, {$point3}, {$point4}, {$point5}))',4326));";
            $placesIDS = Yii::$app->db->createCommand($query)->queryAll();
            $placesIDS = ArrayHelper::getColumn($placesIDS, 'id');

            if ($placesIDS) {

                Yii::$app->db->createCommand()->update('tbl_posts', ['city_id' => $city->id],
                    ['IN', 'id', $placesIDS])->execute();
            }


        }


    }

    public function actionResizeImg()
    {

        $dir = Yii::getAlias('@webroot/post_photo/');

        $files = FileHelper::findFiles($dir);

        foreach ($files as $key => $file) {
            ImageHelper::MaxImg2000($file);
            echo "{$key}\n";
        }


    }


}
