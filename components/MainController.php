<?php

namespace app\components;

use app\models\News;
use app\models\Posts;
use Yii;
use yii\web\Controller;
use app\models\LoginForm;

class MainController extends Controller
{
   public function init()
   {
       if(!yii::$app->user->isGuest){
           $this->layout = 'mainAuth';
       }
   }

   public function getParamsForMainPage()
   {
       $city_name = Yii::$app->request->get('city',['name'=>false])['name'];

       if(!$city_name){
           Yii::$app->city->setDefault();
       }

       //если есть город то прибавляем еще один релэйшен и условия выборки (перенести эту логику в поисковую модель!!!)
       $spotlightQuery = Posts::find()
           ->With('categories')
           ->orderBy([
               'rating'=>SORT_DESC,
               'count_reviews'=>SORT_DESC
           ])
           ->limit(4);

       if($city_name){
           $spotlightQuery->joinWith('city.region')
               ->where(['tbl_city.name'=>$city_name])
               ->orWhere(['tbl_region.name'=>$city_name]);
       }
       $spotlight = $spotlightQuery->all();

       $news = News::find()
           ->with('city.newsCity','city.region.newsRegion','totalView')
           ->limit(4)
           ->all();

       return [
           'spotlight' => $spotlight,
           'news' => $news,
       ];
   }
}
