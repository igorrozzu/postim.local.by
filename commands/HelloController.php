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
use app\models\Posts;
use app\models\Region;
use app\models\Reviews;
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

    public function actionInsertReviews($postId, $userId, $count){
        for ($i = 0; $i < $count; $i++) {
            $review = new Reviews([
                'user_id' => (int)$userId,
                'post_id' => (int)$postId,
                'rating' => 4,
                'like' => 2,
                'date' => time(),
                'data' => 'Все очень вкусно, пришли, заказали роллы. (Очень вкусные, свежие, сочные)
                 Принесли минут за 10, вежливый персонал. Может потому что будний день, все очень 
                 быстро... не знаю. Напитки мгновенно. Нас ничего не смутило. Попробуйте "жареное 
                 молоко" из десертов. Вкусно, ням-нам-ням были первый раз. По возможности 
                 заглянем ещё.',
            ]);

            if ($review->validate() && $review->save()) {
                echo 'review ' . $review->id . " была сохранена\n\r";
            } else {
                echo 'review ' . $review->id . " не была сохранена\n\r";
            }
        }

    }

    public function actionInsertPlaces($userId, $count){
        for ($i = 0; $i < $count; $i++) {
            $model = new Posts([
                'user_id' => (int)$userId,
                'url_name' => 'zvezda-dav',
                'city_id' => 1,
                'cover' => '/post-img/testP.png',
                'date' => time(),
                'rating' => 2,
                'data' => 'Кофе бар довиды',
                'address' => 'ст. метро Партизанская</br>ул. Белгородского полка, 56а',
                'count_reviews' => 10,
                'under_category_id' => 1,
                'count_favorites' => 15,
                'status' => 1
            ]);

            if ($model->validate() && $model->save()) {
                echo 'model ' . $model->id . " была сохранена\n\r";
            } else {
                echo 'model ' . $model->id . " не была сохранена\n\r";
            }
        }

    }
}
