<?php

namespace app\controllers;

use app\components\MainController;
use app\models\entities\FavoritesPost;
use Yii;
use yii\db\Exception;

class PostController extends MainController
{
    public function actionFavoriteState()
    {
        $request = Yii::$app->request;
        if($request->isAjax) {
            $itemId = (int)$request->post('itemId');
            $action = $request->post('action');
            try {
                if ($action === 'add') {
                    $model = new FavoritesPost([
                        'user_id' => Yii::$app->user->id,
                        'post_id' => $itemId
                    ]);
                    $model->save();
                } else if ($action === 'remove') {
                    FavoritesPost::deleteAll([
                        'user_id' => Yii::$app->user->id,
                        'post_id' => $itemId
                    ]);
                }
            } catch (Exception $e) {}
        }
    }
}
