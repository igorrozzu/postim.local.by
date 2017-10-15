<?php

namespace app\controllers;

use app\components\commentsWidget\CommentsNewsWidget;
use app\components\commentsWidget\CommentsPostsWidget;
use app\components\MainController;
use app\components\Pagination;
use app\models\CommentsComplaint;
use app\models\Comments;
use app\models\CommentsLike;
use app\models\entities\OwnerPost;
use app\models\Posts;
use app\models\Reviews;
use app\models\search\CommentsSearch;
use Yii;
use yii\web\Response;

class CommentsController extends MainController
{


    public function actionGetComments(int $id)
    {
        $commentsSearch = new CommentsSearch();
        $paginationComments = new Pagination([
            'pageSize' => Yii::$app->request->get('per-page', 16),
            'page' => Yii::$app->request->get('page', 1) - 1,
            'route' => '/comments/get-comments',
            'selfParams' => [
                'id' => true,
                'type_entity' => true,
            ]
        ]);
        $dataProviderComments = $commentsSearch->search(Yii::$app->request->queryParams,
            $paginationComments,
            $id,
            CommentsSearch::getSortArray('old')
        );

        $widget = CommentsNewsWidget::className();

        if (Yii::$app->request->get('type_entity', 1) == 2) {
            $widget = CommentsPostsWidget::className();
        }

        if (Yii::$app->request->isAjax && !Yii::$app->request->get('_pjax', false)) {
            echo $widget::widget([
                'dataprovider' => $dataProviderComments,
                'is_only_comments' => true
            ]);
        }
    }

    public function actionReloadComments($id)
    {

        $commentsNewsSearch = new CommentsSearch();

        $perpage = Yii::$app->request->get('per-page', 16) + 1;
        if ($perpage < 17) {
            $perpage = 17;
        }
        $paginationComments = new Pagination([
            'pageSize' => $perpage,
            'page' => Yii::$app->request->get('page', 1) - 1,
            'route' => '/news/get-comments',
            'selfParams' => [
                'id' => true,
                'type_entity' => true,
            ]
        ]);
        $dataProviderComments = $commentsNewsSearch->search(Yii::$app->request->queryParams,
            $paginationComments,
            $id,
            CommentsSearch::getSortArray('old')
        );

        $totalComments = Comments::find()->where(['entity_id' => $id])->count();

        $view = 'comments';
        $is_official_user = false;
        if (Yii::$app->request->get('type_entity', 1) == 2) {

            $is_official_user = OwnerPost::find()
                ->innerJoin(Posts::tableName(), Posts::tableName() . '.id = ' . OwnerPost::tableName() . '.post_id')
                ->innerJoin(Reviews::tableName(), Reviews::tableName() . '.post_id = ' . Posts::tableName() . '.id')
                ->where(['owner_id' => Yii::$app->user->getId(),
                    Reviews::tableName() . '.id' => $id
                ])->one();

            $view = 'reviews_comments';
        }

        if (Yii::$app->request->isAjax && !Yii::$app->request->get('_pjax', false)) {
            return $this->renderAjax($view, [
                    'dataProviderComments' => $dataProviderComments,
                    'totalComments' => $totalComments,
                    'id' => $id,
                    'is_official_user' => $is_official_user
                ]
            );
        }
    }

    public function actionAddComments()
    {

        if (!Yii::$app->user->isGuest) {
            $response = new \stdClass();
            $response->status = 'OK';

            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = new Comments();
            if (Yii::$app->request->post('comment_id', false)) {
                $model->setScenario(Comments::$ADD_UNDER_COMMENT);
            } else {
                $model->setScenario(Comments::$ADD_MAIN_COMMENT);
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

    public function actionGetContainerWriteComment(int $id)
    {
        $comment = Comments::find()->with('user')->where(['id' => $id])->one();
        if ($comment) {
            return $this->renderAjax('_write_undercomment', ['comment' => $comment]);
        }

    }

    public function actionDeleteComment()
    {
        $response = new \stdClass();
        $response->status = 'OK';

        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->user->isGuest) {
            $id = Yii::$app->request->post('id', 0);
            $entity_id = Yii::$app->request->post('entity_id', 0);
            $comment = Comments::find()->with(['underComments', 'receiverComment'])->where(['entity_id' => $entity_id, 'id' => $id])->one();
            if (!$comment->underComments) {
                if (!$comment->receiverComment) {
                    if ($comment && $comment->user_id == Yii::$app->user->id) {
                        $comment->delete();
                    } else {
                        $response->status = 'error';
                        $response->message = 'У вас нет прав на удаление комментария';
                    }
                } else {
                    if ($comment && $comment->user_id == Yii::$app->user->id) {
                        Comments::updateAll(['status' => Comments::$STATUS_COMMENT_WAS_DELETED_BY], ['id' => $comment->id]);
                    } else {
                        $response->status = 'error';
                        $response->message = 'У вас нет прав на удаление комментария';
                    }
                }

            } else {
                if ($comment && $comment->user_id == Yii::$app->user->id) {
                    Comments::updateAll(['status' => Comments::$STATUS_COMMENT_WAS_DELETED_BY], ['id' => $comment->id]);
                } else {
                    $response->status = 'error';
                    $response->message = 'У вас нет прав на удаление комментария';
                }
            }

        }
        return $response;
    }

    public function actionAddRemoveLikeComment(int $id)
    {
        $response = new \stdClass();
        $response->status = 'OK';
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->user->isGuest) {
            $comment = Comments::find()->with('likeUser')->where(['id' => $id])->one();
            if ($comment && $comment->likeUser == null) {
                if ($comment->updateCounters(['like' => 1])) {
                    $commentsLike = new CommentsLike(['comment_id' => $comment->id,
                        'user_id' => Yii::$app->user->id]);
                    if ($commentsLike->validate() && $commentsLike->save()) {
                        $response->status = 'add';
                    }
                }

            } else {
                if ($comment->updateCounters(['like' => -1])) {
                    if ($comment->likeUser->delete()) {
                        $response->status = 'remove';
                    }
                }
            }

            $response->count = $comment->like;
        }
        return $response;
    }

    public function actionComplainComment()
    {
        $response = new \stdClass();
        $response->status = 'OK';
        $response->message = 'Спасибо, что помогаете!<br>Ваша жалоба будет рассмотрена модераторами';
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->user->isGuest) {
            $comment_id = Yii::$app->request->post('id', null);
            $message = Yii::$app->request->post('message', null);
            $commentComplaint = new CommentsComplaint(['comment_id' => $comment_id,
                'message' => $message,
                'user_id' => Yii::$app->user->id
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

}
