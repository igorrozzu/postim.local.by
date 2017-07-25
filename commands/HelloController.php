<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Category;
use app\models\City;
use app\models\Notification;
use app\models\Region;
use app\models\UnderCategory;
use yii\console\Controller;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelloController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionInsertCity()
    {
        $row = 1;
        if (($handle = fopen(\Yii::getAlias('@app/web/tmp/goroda.csv'), "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $num = count($data);
                $row++;
                $region = new Region();
                $region->name=$data[0];
                $region->url_name=$data[1];
                if($region->validate() && $region->save()){
                    echo 'Регион '.$region->name." был сохранен\n\r";
                    for ($c=2; $c < $num; $c+=2) {
                        $city = new City();
                        $city->name =$data[$c];
                        $city->url_name =$data[$c+1];
                        $city->link('region',$region);
                        if($city->validate() && $city->save()){
                             echo 'города '.$city->url_name." был сохранен\n\r";
                        }else{
                            echo 'города '.$city->url_name."не был сохранен\n\r";
                        }
                    }
                }else{
                    echo 'Регион '.$region->name." не был сохранен\n\r";
                }

            }
            fclose($handle);
        }

    }

    public function actionInsertCategory(){
        $row = 1;
        if (($handle = fopen(\Yii::getAlias('@app/web/tmp/cat.csv'), "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 10000, ";")) !== FALSE) {
                $num = count($data);
                $row++;
                $category = new Category();
                $category->name=$data[0];
                $category->url_name=$data[1];
                if($category->validate() && $category->save()){
                    echo 'Категория '.$category->name." была сохранена\n\r";
                    for ($c=2; $c < $num; $c+=2) {
                        $under_cat = new UnderCategory();
                        $under_cat->name =$data[$c];
                        $under_cat->url_name =$data[$c+1];
                        $under_cat->link('category',$category);
                        if($under_cat->validate() && $under_cat->save()){
                            echo 'под категория '.$under_cat->url_name." был сохранен\n\r";
                        }else{
                            echo 'под категория '.$under_cat->url_name."не был сохранен\n\r";
                        }
                    }
                }else{
                    echo 'Регион '.$category->name." не был сохранен\n\r";
                }

            }
            fclose($handle);
        }
    }

    public function actionInsertNotif($idFrom, $idTo, $count){
        for ($i = 0; $i < $count; $i++) {
            $notif = new Notification();
            $notif->user_id = $idTo;
            $notif->sender_id = $idFrom;
            $notif->message = json_encode([
                'type' => 'xz',
                'data' => 'Получен новый отзыв о <b>Музей-заповедник "Коломенское"</b>'
            ]);
            $notif->date = time();
            if ($notif->validate() && $notif->save()) {
                echo 'Notif ' . $notif->id . " была сохранена\n\r";
            } else {
                echo 'Notif ' . $notif->id . " не была сохранена\n\r";
            }
        }

    }


}
