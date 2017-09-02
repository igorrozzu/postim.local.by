<?php

namespace app\controllers;

use app\components\cardsNewsWidget\CardsNewsWidget;
use app\components\commentsWidget\CommentsNewsWidget;
use app\components\Helper;
use app\components\MainController;
use app\components\Pagination;
use app\models\CommentsComplaint;
use app\models\CommentsNews;
use app\models\CommentsNewsLike;
use app\models\entities\FavoritesNews;
use app\models\News;
use app\models\PostsSearch;
use app\models\search\CommentsNewsSearch;
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

        $commentsNewsSearch = new CommentsNewsSearch();

        $defaultLimit = isset($comment_id) ? 1000 : 16;
        $paginationComments= new Pagination([
            'pageSize' => Yii::$app->request->get('per-page', $defaultLimit),
            'page' => Yii::$app->request->get('page', 1) - 1,
            'route' => '/news/get-comments',
            'selfParams'=>[
                'id'=>true,
            ]
        ]);

        $dataProviderComments = $commentsNewsSearch->search( Yii::$app->request->queryParams,
            $paginationComments,
            $news['id'],
            CommentsNewsSearch::getSortArray('old')
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

    public function actionGetComments($id)
    {
        $commentsNewsSearch = new CommentsNewsSearch();
        $paginationComments= new Pagination([
            'pageSize' => Yii::$app->request->get('per-page', 16),
            'page' => Yii::$app->request->get('page', 1) - 1,
            'route' => '/news/get-comments',
            'selfParams'=>[
                'id'=>true,
            ]
        ]);
        $dataProviderComments = $commentsNewsSearch->search( Yii::$app->request->queryParams,
            $paginationComments,
            $id,
            CommentsNewsSearch::getSortArray('old')
        );

        if (Yii::$app->request->isAjax && !Yii::$app->request->get('_pjax', false)) {
           echo CommentsNewsWidget::widget([
               'dataprovider'=>$dataProviderComments,
               'is_only_comments'=>true
           ]);
        }
    }

    public function actionReloadComments($id){

        $commentsNewsSearch = new CommentsNewsSearch();

        $perpage =  Yii::$app->request->get('per-page', 16)+1;
        if($perpage<17){
            $perpage=17;
        }
        $paginationComments= new Pagination([
            'pageSize' => $perpage,
            'page' => Yii::$app->request->get('page', 1) - 1,
            'route' => '/news/get-comments',
            'selfParams'=>[
                'id'=>true,
            ]
        ]);
        $dataProviderComments = $commentsNewsSearch->search( Yii::$app->request->queryParams,
            $paginationComments,
            $id,
            CommentsNewsSearch::getSortArray('old')
        );

        $totalComments = CommentsNews::find()->where(['news_id'=>$id])->count();

        if (Yii::$app->request->isAjax && !Yii::$app->request->get('_pjax', false)) {
            return $this->renderAjax('comments', [
                    'dataProviderComments' => $dataProviderComments,
                    'totalComments' => $totalComments
                ]
            );
        }
    }

    public function actionAddComments(){

        if(!Yii::$app->user->isGuest){
            $response = new \stdClass();
            $response->status='OK';

            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = new CommentsNews();
            if(Yii::$app->request->post('comment_id',false)){
                $model->setScenario(CommentsNews::$ADD_UNDER_COMMENT);
            }else{
                $model->setScenario(CommentsNews::$ADD_MAIN_COMMENT);
            }

            $model->user_id = Yii::$app->user->id;
            $model->load(Yii::$app->request->post(), '');
            if ($model->validate() && $model->save()) {
                return $response;
            } else {
                $name_attribute = key($model->getErrors());
                $response->status = 'error';
                $response->message = $model->getFirstError($name_attribute);
            }

            return $response;
        }

    }

    public function actionGetContainerWriteComment(int $id){
        $comment = CommentsNews::find()->with('user')->where(['id'=>$id])->one();
        if($comment){
            return $this->renderAjax('_write_undercomment', ['comment' => $comment]);
        }

    }

    public function actionDeleteComment(){
        $response = new \stdClass();
        $response->status='OK';

        Yii::$app->response->format = Response::FORMAT_JSON;

        if(!Yii::$app->user->isGuest){
            $id = Yii::$app->request->post('id',0);
            $news_id =Yii::$app->request->post('news_id',0);
            $comment = CommentsNews::find()->with('underComments')->where(['news_id'=>$news_id,'id'=>$id])->one();
            if(!$comment->underComments){
                if($comment && $comment->user_id == Yii::$app->user->id){
                    $comment->delete();
                }else{
                    $response->status='error';
                    $response->message='У вас нет прав на удаление комментария';
                }
            }else{
                if($comment && $comment->user_id == Yii::$app->user->id){
                    $comment->status=CommentsNews::$STATUS_COMMENT_WAS_DELETED_BY;
                    $comment->update();
                }else{
                    $response->status='error';
                    $response->message='У вас нет прав на удаление комментария';
                }
            }

        }
        return $response;
    }

    public function actionAddRemoveLikeComment(int $id){
        $response = new \stdClass();
        $response->status='OK';
        Yii::$app->response->format = Response::FORMAT_JSON;

        if(!Yii::$app->user->isGuest){
            $comment = CommentsNews::find()->with('likeUser')->where(['id'=>$id])->one();
            if($comment && $comment->likeUser==null){
                if($comment->updateCounters(['like' => 1])){
                    $commentsNewsLike= new CommentsNewsLike(['comment_id'=>$comment->id,
                        'user_id'=>Yii::$app->user->id]);
                    if($commentsNewsLike->validate() && $commentsNewsLike->save()){
                        $response->status='add';
                    }
                }

            }else{
                if($comment->updateCounters(['like' => -1])){
                    if($comment->likeUser->delete()){
                        $response->status='remove';
                    }
                }
            }

            $response->count=$comment->like;
        }
        return $response;
    }

    public function actionComplainComment(){
        $response = new \stdClass();
        $response->status='OK';
        $response->message='Спасибо, что помогаете!<br>Ваша жалоба будет рассмотрена модераторами';
        Yii::$app->response->format = Response::FORMAT_JSON;

        if(!Yii::$app->user->isGuest){
            $comment_id =Yii::$app->request->post('id',null);
            $message = Yii::$app->request->post('message',null);
            $commentComplaint = new CommentsComplaint(['comment_id' => $comment_id,
                'message'=>$message,
                'user_id'=>Yii::$app->user->id
            ]);

            if ($commentComplaint->validate() && $commentComplaint->save()) {
                return $response;
            } else {
                $name_attribute = key($commentComplaint->getErrors());
                $response->status = 'error';
                $response->message = $commentComplaint->getFirstError($name_attribute);
            }
            return $response;
        }
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
