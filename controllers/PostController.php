<?php

namespace app\controllers;

use app\components\MainController;
use app\models\entities\FavoritesPost;
use app\models\Posts;
use Yii;
use yii\web\Response;

class PostController extends MainController
{
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
