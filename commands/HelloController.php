<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Category;
use app\models\City;
use app\models\Comments;
use app\models\News;
use app\models\Notification;
use app\models\PostCategoryCount;
use app\models\Posts;
use app\models\PostUnderCategory;
use app\models\Region;
use app\models\Reviews;
use app\models\TotalView;
use app\models\UnderCategory;
use app\models\WorkingHours;
use yii\console\Controller;
use yii\base\Security;

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

    public function actionInsertNotif($idFrom, $count){
        for ($i = 0; $i < $count; $i++) {
            $notif = new Notification();
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
                'city_id' => 23,
                'cover' => '/post-img/testP.png',
                'date' => time(),
                'rating' => 4,
                'data' => 'Кофе бар довиды',
                'address' => 'ст. метро Партизанская</br>ул. Белгородского полка, 56а',
                'count_reviews' => 10,
                'under_category_id' => 1,
                'count_favorites' => 0,
                'status' => 1
            ]);

            if ($model->validate() && $model->save()) {
                echo 'model ' . $model->id . " была сохранена\n\r";
            } else {
                echo 'model ' . $model->id . " не была сохранена\n\r";
            }
        }

    }

    public function actionInsertNews($count){
        $generateString = new Security();
        for ($i = 0; $i < $count; $i++) {
            $total_view = new TotalView(['count'=>rand(1,142)]);
            if($total_view->save()){
                $model = new News([
                    'city_id'=>rand(1,142),
                    'header'=>'В витебске открыли музей искуства',
                    'description'=>'В витебске открыли музей искуства',
                    'date'=>time(),
                    'data'=>'<div class="block-photo-post">
        <img src="img/photo-post-news.jpg">
        <div class="photo-desc">Фото: Life.ru</div>
      </div>
      <div class="post-text">С сегодняшнего дня в рейтинге компании будет учитываться только последняя по времени оценка, если автор писал
        несколько отзывов об этой компании. Почему? Рассказываем в блоге.</div>
      <h2 class="post-h2">Как было раньше</h2>
      <div class="post-text">Обратился  — написал!» — примерно так мог бы звучать девиз опытных фламперов</div>
      <div class="post-text">Именно поэтому на страницах многих филиалов можно увидеть не по одному, а по несколько отзывов одного и того же
        автора. Например, о кафе, о кинотеатре или о магазине неподалёку от дома — каждый раз, как происходит что-то,
        заслуживающее внимания (в кинозале поставили кондиционер, в кафе начали делать потрясающие десерты, в магазине на
        кассах стали медленнее обслуживать), у флампера появляется повод написать об этой компании новый отзыв.</div>
      <div class="post-text">До сегодняшнего дня мы учитывали в рейтинге компании каждую оценку, которую вы когда-либо ей ставили, не обращая
        внимания на то, сколько раз вы писали о ней и как давно происходили эти события.</div>
      <h2 class="post-h2">Как теперь</h2>
      <div class="block-photo-post">
        <img src="img/photo-post-news600px.jpg">
        <div class="photo-desc">Фото: Life.ru</div>
      </div>
      <div class="post-text">Обратился  — написал!» — примерно так мог бы звучать девиз опытных фламперов</div>
      <div class="post-text">Именно поэтому на страницах многих филиалов можно увидеть не по одному, а по несколько отзывов одного и того же
        автора. Например, о кафе, о кинотеатре или о магазине неподалёку от дома — каждый раз, как происходит что-то,
        заслуживающее внимания (в кинозале поставили кондиционер, в кафе начали делать потрясающие десерты, в магазине на
        кассах стали медленнее обслуживать), у флампера появляется повод написать об этой компании новый отзыв.</div>',
                    'description_s'=>'В витебске открыли музей искуства',
                    'key_word_s'=>'В витебске открыли музей искуства',
                    'count_favorites'=> 0,
                    'cover' => '/post-img/testP.png',
                    'url_name'=>$generateString->generateRandomString()
                ]);

                $model->link('totalView',$total_view);

                if ($model->validate() && $model->save()) {
                    echo 'model ' . $model->id . " была сохранена\n\r";
                } else {
                    echo 'model ' . $model->id . " не была сохранена\n\r";
                }
            }

        }

    }

    public function actionInsertCommentsNews($id_user,$id_news,$count_comments,$is_under=false){
        for($i=0;$i<$count_comments;$i++){
            $model = new CommentsNews();
            $model->user_id=$id_user;
            $model->news_id=$id_news;
            $model->like=rand(1,1210);
            $model->date=time();
            $model->data='Текст главного комментария';
            if($model->save()){
                echo "главный комментарий был сохранен под id ".$model->id."\n\r";
                if($is_under){
                    $model_under= new CommentsNews([
                        'user_id'=>$id_user,
                        'entity_id'=>$id_news,
                        'like'=>rand(1,1210),
                        'date'=>time(),
                        'data'=>'Текст ответа на главный комментарий',
                        'main_comment_id'=>$model->id
                    ]);
                    $model_under->save();
                }
            }

        }
    }

    public function actionConvertTime(){
        $workingHours = WorkingHours::find()->all();
        foreach ($workingHours as $working){
            if($working->time_start > $working->time_finish || ($working->time_start == 0 && $working->time_finish == 0) ){
                $working->time_finish+=24*3600;
                if($working->save()){
                    echo "был сохранен под id ".$working->id."\n\r";
                }else{
                    echo "не был сохранен под id ".$working->id."\n\r";
                }

            }
        }
    }

    public function actionCalcCountPlace(){

        $posts =  Posts::find()
            ->with('categories.category')
            ->with('city.region.coutries')
            ->all();



        foreach ($posts as $post){
            $is_has_category = [];
            foreach ($post->categories as $under_category){

                $this->savePostCategoryCount($under_category->url_name,$post->city->name);
                $this->savePostCategoryCount($under_category->url_name,$post->city->region->name);
                $this->savePostCategoryCount($under_category->url_name,$post->city->region->coutries->name);
                if(!isset($is_has_category[$under_category->category->url_name.$post->city->name])){
                    $this->savePostCategoryCount($under_category->category->url_name,$post->city->name);
                    $is_has_category[$under_category->category->url_name.$post->city->name]=true;
                }

                if(!isset($is_has_category[$under_category->category->url_name.$post->city->region->name])){
                    $this->savePostCategoryCount($under_category->category->url_name,$post->city->region->name);
                    $is_has_category[$under_category->category->url_name.$post->city->region->name]=true;
                }

                if(!isset($is_has_category[$under_category->category->url_name.$post->city->region->coutries->name])){
                    $this->savePostCategoryCount($under_category->category->url_name,$post->city->region->coutries->name);
                    $is_has_category[$under_category->category->url_name.$post->city->region->coutries->name]=true;
                }

            }

        }
    }

    private function savePostCategoryCount($category_name,$city_name){
        $model = PostCategoryCount::find()
            ->where(['category_url_name'=>$category_name])
            ->andWhere(['city_name'=>$city_name])->one();
        if($model != null){
            $model->updateCounters(['count' => 1]);
        }else{
            $model = new PostCategoryCount(['category_url_name' => $category_name,
                                            'city_name'=>$city_name,'count'=>1]);
            $model->save();
        }
    }


}
