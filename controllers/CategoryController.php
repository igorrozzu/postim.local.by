<?php

namespace app\controllers;

use app\components\MainController;
use app\models\Posts;
use app\models\PostsSearch;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;

class CategoryController extends MainController
{

    public $category=false;
    public $under_category =false;

    public function actionIndex()
    {
        $this->category = Yii::$app->request->get('category',false);
        $this->under_category =Yii::$app->request->get('under_category',false);

        $searchModel = new PostsSearch();
        $pagination = new Pagination([
            'pageSize' => Yii::$app->request->get('per-page', 16),
            'page' => Yii::$app->request->get('page', 1)-1,
        ]);

        $paramSort = Yii::$app->request->get('sort', 'rating');
        $sort = PostsSearch::getSortArray($paramSort);


        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            $pagination,
            $sort
        );

        $url = $this->under_category?$this->under_category['url_name']:$this->category['url_name'];

        $params=[
            'dataProvider'=>$dataProvider,
            'sort'=>$paramSort,
            'url'=> $url
        ];

        return $this->render('index',$params);
    }

}
