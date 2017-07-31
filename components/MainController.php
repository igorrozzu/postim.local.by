<?php

namespace app\components;

use app\models\News;
use app\models\Posts;
use app\models\PostsSearch;
use app\models\search\NewsSearch;
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

       $searchModel = new PostsSearch();
       $pagination = new Pagination([
           'pageSize' => Yii::$app->request->get('per-page', 4),
           'page' => Yii::$app->request->get('page', 1)-1,
       ]);
       $sort =[
           'rating'=>SORT_DESC,
           'count_reviews'=>SORT_DESC
       ];

       $dataProvider = $searchModel->search(
           Yii::$app->request->queryParams, $pagination, $sort, time()
       );

       $searchModel = new NewsSearch();
       $newsPagination = new Pagination([
           'pageSize' => Yii::$app->request->get('per-page', 4),
           'page' => Yii::$app->request->get('page', 1)-1,
       ]);

       $newsProvider = $searchModel->search(
           Yii::$app->request->queryParams, $newsPagination,
           PostsSearch::getSortArray('new'), time()
       );

       return [
           'spotlight' => $dataProvider,
           'news' => $newsProvider,
       ];
   }
}
