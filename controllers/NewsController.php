<?php

namespace app\controllers;

use app\components\cardsNewsWidget\CardsNewsWidget;
use app\components\commentsWidget\CommentsNewsWidget;
use app\components\Helper;
use app\components\MainController;
use app\components\Pagination;
use app\models\Comments;
use app\models\CommentsLike;
use app\models\entities\FavoritesNews;
use app\models\News;
use app\models\PostsSearch;
use app\models\search\CommentsSearch;
use app\models\search\NewsSearch;
use app\models\Region;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;

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
        $title = '';
        $breadcrumbParams = $this->getParamsForBreadcrumb($h1,$title);


        if (Yii::$app->request->isAjax && !Yii::$app->request->get('_pjax', false)) {
            echo CardsNewsWidget::widget(['dataprovider' => $dataProvider, 'settings' => ['replace-container-id' => 'feed-news','load-time'=>$loadTime]]);
        } else {
            return $this->render('index', [
                'dataProvider' => $dataProvider,
                'breadcrumbParams'=>$breadcrumbParams,
                'h1'=>$h1,
                'title'=>$title,
                'loadTime'=>$loadTime
            ]);
        }

    }

    public function actionNews(int $id, int $comment_id = null) {
        $loadTime = Yii::$app->request->get('loadTime',time());
        $newsQuery = News::find()->with('totalView')->with('city.region')->where(['id'=>$id]);
        if(!Yii::$app->user->isGuest){
            $newsQuery->with('hasLike');
        }
        $news = $newsQuery->one();

        Helper::addViews($news->totalView);

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

        $commentsSearch = new CommentsSearch();

        $defaultLimit = isset($comment_id) ? 1000 : 16;
        $_GET['type_entity']=1;
        $paginationComments= new Pagination([
            'pageSize' => Yii::$app->request->get('per-page', $defaultLimit),
            'page' => Yii::$app->request->get('page', 1) - 1,
            'route' => '/comments/get-comments',
            'selfParams'=>[
                'id'=>true,
				'type_entity'=>true
            ]
        ]);

        $dataProviderComments = $commentsSearch->search( Yii::$app->request->queryParams,
            $paginationComments,
            $news['id'],
            CommentsSearch::getSortArray('old')
        );


        $breadcrumbParams = $this->getParamsForBreadcrumbInside($news);

        return $this->render('inside_news',[
            'news'=>$news,
            'lastNews'=>$lastNews,
            'breadcrumbParams'=>$breadcrumbParams,
            'dataProviderComments'=>$dataProviderComments,
            'loadTime'=>$loadTime
        ]);
    }


    private function getParamsForBreadcrumb(&$h1,&$title){
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
        $name='Новости '.Yii::t('app/parental_slope','Беларусь');
        $title = 'Новости '.Yii::t('app/parental_slope','Беларусь').', последние новости - Postim.by';
        if($city = Yii::$app->request->get('city')){
            $is_region = Region::find()->select('url_name')->where(['url_name'=>$city['url_name']])->one();
            if($is_region){
                $name ='Новости '.Yii::t('app/parental_slope',$city['name']);
                $title = 'Новости '.Yii::t('app/parental_slope',$city['name']).', последние новости области - Postim.by';
            }else{
                $name ='Новости '.Yii::t('app/parental_slope',$city['name']).' и области';
                $title = 'Новости '.Yii::t('app/parental_slope',$city['name']).', последние новости района и области - Postim.by';
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

    public function actionFavoriteState()
    {
        $response = new \stdClass();
        $response->status='OK';
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;
        if( $request->isAjax && !Yii::$app->user->isGuest) {
            $itemId = (int)$request->post('itemId');

            $news = News::find()->select('count_favorites,id')->with('hasLike')->where(['id'=>$itemId])->one();

            if($news->hasLike){
                if($news->updateCounters(['count_favorites' => -1])){
                    if($news->hasLike->delete()){
                        $response->status='remove';
                    }
                }
            }else{
                if($news->updateCounters(['count_favorites' => 1])){
                    $model = new FavoritesNews([
                        'user_id'=>Yii::$app->user->id,
                        'news_id'=>$news->id
                    ]);
                    if($model->save()){
                        $response->status='add';
                    }
                }

            }
            $response->count=$news->count_favorites;

        }
        return $response;
    }
}
