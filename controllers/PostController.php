<?php

namespace app\controllers;

use app\components\Helper;
use app\components\MainController;
use app\models\entities\FavoritesPost;
use app\models\Posts;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class PostController extends MainController
{

    public function actionIndex(int $id){

         $post = Posts::find()->with([
                'info', 'workingHours',
                'city', 'totalView',
                'hasLike','categories.category'])
            ->where(['id'=>$id])
            ->one();

        if($post){
            Helper::addViews($post->totalView);
            $breadcrumbParams = $this->getParamsForBreadcrumb($post);
            return $this->render('index',['post'=>$post,'breadcrumbParams'=>$breadcrumbParams]);
        }else{
            throw new NotFoundHttpException();
        }

    }

    public function getParamsForBreadcrumb($post){
        $breadcrumbParams=[];

        $currentUrl = Yii::$app->getRequest()->getHostInfo();
        $breadcrumbParams[] = [
            'name' => ucfirst(Yii::$app->getRequest()->serverName),
            'url_name' => $currentUrl,
            'pjax' => 'class="main-header-pjax a"'
        ];

        if($post->city){
            $currentUrl=$currentUrl.'/'.$post->city['url_name'];
            $breadcrumbParams[]=[
                'name'=>$post->city['name'],
                'url_name'=>$currentUrl,
                'pjax'=>'class="main-header-pjax a"'
            ];
        }

        if(isset($post->categories->category)){
            $currentUrl=$currentUrl.'/'.$post->categories->category['url_name'];
            $breadcrumbParams[]=[
                'name'=>$post->categories->category['name'],
                'url_name'=>$currentUrl,
                'pjax'=>'class="main-header-pjax a"'
            ];
        }

        if($post->categories){
            $currentUrl=$currentUrl.'/'.$post->categories['url_name'];
            $breadcrumbParams[]=[
                'name'=>$post->categories['name'],
                'url_name'=>$currentUrl,
                'pjax'=>'class="main-header-pjax a"'
            ];
        }

        $breadcrumbParams[]=[
            'name'=>$post['data'],
            'url_name'=>$post['url_name'].'-p'.$post['id'],
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

            $post = Posts::find()->select('count_favorites,id')->with('hasLike')->where(['id'=>$itemId])->one();

            if($post->hasLike){
                if($post->updateCounters(['count_favorites' => -1])){
                    if($post->hasLike->delete()){
                        $response->status='remove';
                    }
                }
            }else{
                if($post->updateCounters(['count_favorites' => 1])){
                    $model = new FavoritesPost([
                        'user_id'=>Yii::$app->user->id,
                        'post_id'=>$post->id
                    ]);
                    if($model->save()){
                        $response->status='add';
                    }
                }

            }
            $response->count=$post->count_favorites;

        }
        return $response;
    }
}
