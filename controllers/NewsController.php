<?php

namespace app\controllers;

use app\components\cardsNewsWidget\CardsNewsWidget;
use app\components\MainController;
use app\components\Pagination;
use app\models\CommentsNews;
use app\models\News;
use app\models\PostsSearch;
use app\models\search\NewsSearch;
use app\models\Region;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\Controller;

class NewsController extends MainController
{


    public function actionIndex()
    {

        $loadTime = Yii::$app->request->get('loadTime',time());

        $searchModel = new NewsSearch();
        $pagination = new Pagination([
            'pageSize' => Yii::$app->request->get('per-page', 6),
            'page' => Yii::$app->request->get('page', 1) - 1,
            'route' => Yii::$app->request->getPathInfo()
        ]);

        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            $pagination,
            PostsSearch::getSortArray('new'),
            $loadTime
        );

        $h1='';
        $breadcrumbParams = $this->getParamsForBreadcrumb($h1);


        if (Yii::$app->request->isAjax && !Yii::$app->request->get('_pjax', false)) {
            echo CardsNewsWidget::widget(['dataprovider' => $dataProvider, 'settings' => ['replace-container-id' => 'feed-news','load-time'=>$loadTime]]);
        } else {
            return $this->render('index', [
                'dataProvider' => $dataProvider,
                'breadcrumbParams'=>$breadcrumbParams,
                'h1'=>$h1,
                'loadTime'=>$loadTime
            ]);
        }

    }

    public function actionNews($id){
        $loadTime = Yii::$app->request->get('loadTime',time());
        $news = News::find()->with('totalView')->with('city.region')->where(['id'=>$id])->one();

        $searchModel = new NewsSearch();
        $pagination = new Pagination([
            'pageSize' => Yii::$app->request->get('per-page', 6),
            'page' => Yii::$app->request->get('page', 1) - 1,
            'route' => ($news['city']['url_name']?'/'.$news['city']['url_name']:'').'/novosti'
        ]);

        $lastNews = $searchModel->search(
            ['city' =>
                [
                    'name' => $news['city']['name'],
                    'url_name'=>$news['city']['url_name']
                ]
            ],
            $pagination,
            PostsSearch::getSortArray('new'),
            $loadTime
        );

        $queryComments = CommentsNews::find()
            ->with('underComments.user.userInfo')
            ->with('user.userInfo')
            ->where([
                'news_id'=>$news['id'],
                'main_comment_id'=>null
            ]);
        $paginationComments= new Pagination([
            'pageSize' => Yii::$app->request->get('per-page', 2),
            'page' => Yii::$app->request->get('page', 1) - 1,
            'route' => '/news/get-comments',
            'selfParams'=>[
                'id'=>true,
            ]
        ]);
        $dataProviderComments = new ActiveDataProvider([
            'query' => $queryComments,
            'pagination' => $paginationComments
        ]);


        $breadcrumbParams = $this->getParamsForBreadcrumbInside($news);

        return $this->render('inside_news',[
            'news'=>$news,
            'lastNews'=>$lastNews,
            'breadcrumbParams'=>$breadcrumbParams,
            'dataProviderComments'=>$dataProviderComments,
            'loadTime'=>$loadTime
        ]);
    }

    private function getParamsForBreadcrumb(&$h1){
        $breadcrumbParams=[];

        $currentUrl = Yii::$app->getRequest()->getHostInfo();
        $breadcrumbParams[] = [
            'name' => ucfirst(Yii::$app->getRequest()->serverName),
            'url_name' => $currentUrl,
            'pjax' => 'class="main-header-pjax a"'
        ];

        if($city = Yii::$app->request->get('city')){
            $currentUrl=$currentUrl.'/'.$city['url_name'];
            $breadcrumbParams[]=[
                'name'=>$city['name'],
                'url_name'=>$currentUrl,
                'pjax'=>'class="main-pjax a"'
            ];
        }

        $currentUrl=$currentUrl.'/'.'novosti';
        $name='Новости в '.Yii::t('app/locativus','Беларусь');
        if($city = Yii::$app->request->get('city')){
            $is_region = Region::find()->select('url_name')->where(['url_name'=>$city['url_name']])->one();
            if($is_region){
                $name ='Новости в '.Yii::t('app/locativus',$city['name']);
            }else{
                $name ='Новости в '.Yii::t('app/locativus',$city['name']).' и области';
            }
        }
        $h1 = $name;

        $breadcrumbParams[]=[
            'name'=>$name,
            'url_name'=>$currentUrl,
            'pjax'=>'class="main-pjax a"'
        ];




        return $breadcrumbParams;
    }

    private function getParamsForBreadcrumbInside($news){
        $breadcrumbParams=[];

        $currentUrl = Yii::$app->getRequest()->getHostInfo();
        $breadcrumbParams[] = [
            'name' => ucfirst(Yii::$app->getRequest()->serverName),
            'url_name' => $currentUrl,
            'pjax' => 'class="main-header-pjax a"'
        ];

        if($news->city){
            $currentUrl=$currentUrl.'/'.$news->city['url_name'];
            $breadcrumbParams[]=[
                'name'=>$news->city['name'],
                'url_name'=>$currentUrl,
                'pjax'=>'class="main-header-pjax a"'
            ];
        }

        $currentUrl=$currentUrl.'/'.'novosti';
        $name='Новости в '.Yii::t('app/locativus','Беларусь');
        if($city = $news->city){
            if($city['name']==$city->region['name']){
                $name ='Новости в '.Yii::t('app/locativus',$city['name']);
            }else{
                $name ='Новости в '.Yii::t('app/locativus',$city['name']).' и области';
            }
        }

        $breadcrumbParams[]=[
            'name'=>$name,
            'url_name'=>$currentUrl,
            'pjax'=>'class="main-header-pjax a"'
        ];

        $breadcrumbParams[]=[
            'name'=>$news['header'],
            'url_name'=>$news['url_name'].'-n'.$news['id'],
            'pjax'=>'class="main-pjax a"'
        ];

        return $breadcrumbParams;



    }


}
