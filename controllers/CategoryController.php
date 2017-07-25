<?php

namespace app\controllers;

use app\components\cardsPlaceWidget\CardsPlaceWidget;
use app\components\MainController;
use app\models\Posts;
use app\models\PostsSearch;
use Yii;
use app\components\Pagination;
use yii\helpers\Url;
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
            'pageSize' => Yii::$app->request->get('per-page', 2),
            'page' => Yii::$app->request->get('page', 1)-1,
            'route'=>Yii::$app->request->getPathInfo(),
            'selfParams'=>[
                'sort'=>true,
            ]
        ]);

        $paramSort = Yii::$app->request->get('sort', 'rating');
        $sort = PostsSearch::getSortArray($paramSort);


        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            $pagination,
            $sort
        );

        $url = $this->under_category?$this->under_category['url_name']:$this->category['url_name'];

        $breadcrumbParams = $this->getParamsForBreadcrumb();

        $params=[
            'dataProvider'=>$dataProvider,
            'sort'=>$paramSort,
            'url'=> $url,
            'breadcrumbParams'=>$breadcrumbParams,
        ];

        if(Yii::$app->request->isAjax && !Yii::$app->request->get('_pjax',false) ){
            echo CardsPlaceWidget::widget(['dataprovider' => $dataProvider,'settings'=>['show-more-btn'=>true]]);
        }else{
            return $this->render('index',$params);
        }

    }

    private function getParamsForBreadcrumb(){
        $breadcrumbParams=[];

        $currentUrl = Yii::$app->getRequest()->getHostInfo();
        $breadcrumbParams[] = [
            'name' => ucfirst(Yii::$app->getRequest()->serverName),
            'url_name' => $currentUrl,
            'pjax' => false
        ];

        if($city = Yii::$app->request->get('city')){
            $currentUrl=$currentUrl.'/'.$city['url_name'];
            $breadcrumbParams[]=[
                'name'=>$city['name'],
                'url_name'=>$currentUrl,
                'pjax'=>false
            ];
        }

        $breadcrumbParams[]=[
            'name'=>$this->category['name'],
            'url_name'=>$currentUrl.'/'.$this->category['url_name'],
            'pjax'=>true
        ];

        if($this->under_category){
            $breadcrumbParams[]=[
                'name'=>$this->under_category['name'],
                'url_name'=>$currentUrl.'/'.$this->under_category['url_name'],
                'pjax'=>true
            ];
        }


        return $breadcrumbParams;
    }

}
