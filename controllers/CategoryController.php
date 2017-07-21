<?php

namespace app\controllers;

use app\components\MainController;
use app\models\Posts;
use Yii;
use yii\web\Controller;

class CategoryController extends MainController
{

    public $category=false;
    public $under_category =false;

    public function actionIndex()
    {
        $city_name = Yii::$app->request->get('city',['name'=>false])['name'];
        $this->category = Yii::$app->request->get('category',false);
        $this->under_category =Yii::$app->request->get('under_category',false);

        //если есть город то прибавляем еще один релэйшен и условия выборки (перенести эту логику в поисковую модель!!!)
        $postsQuery = Posts::find()
            ->joinWith('categories.category')
            ->orderBy([
                'rating'=>SORT_DESC,
                'count_reviews'=>SORT_DESC
            ])
            ->limit(16);

        if($city_name){
            $postsQuery->joinWith('city.region')
                ->where(['tbl_city.name'=>$city_name])
                ->orWhere(['tbl_region.name'=>$city_name]);
        }

        if( $this->under_category){
            $postsQuery->andWhere(['tbl_under_category.url_name'=> $this->under_category['url_name']]);
        }else{
            $postsQuery->andWhere(['tbl_category.url_name'=>$this->category['url_name']]);
        }

        $posts = $postsQuery->all();

        $params=[
            'posts'=>$posts,
        ];

        return $this->render('index',$params);
    }



}
