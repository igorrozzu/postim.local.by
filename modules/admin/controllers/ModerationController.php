<?php

namespace app\modules\admin\controllers;

use app\components\UserHelper;
use app\models\AddPost;
use app\models\Posts;
use app\modules\admin\models\post\PostsModeration;
use app\modules\admin\models\post\PostsModerationSearch;
use app\modules\admin\models\Reviews;
use app\modules\admin\components\AdminDefaultController;
use app\modules\admin\models\Complaints;
use app\modules\admin\models\ComplaintsSearch;
use app\components\Pagination;
use app\modules\admin\models\Gallery;
use app\modules\admin\models\GallerySearch;
use app\modules\admin\models\ReviewsSearch;
use yii\db\ActiveRecord;
use yii\web\Controller;

/**
 * Default controller for the `admin` module
 */
class ModerationController extends AdminDefaultController
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {


    }

    public function actionComplaints(){

        $searchModel = new ComplaintsSearch();
        $pagination = new Pagination([
            'pageSize' => \Yii::$app->request->get('per-page', 8),
            'page' => \Yii::$app->request->get('page', 1)-1,
            'route'=>'/admin/moderation/complaints',
            'selfParams'=>[
                'sort'=>true,
            ]
        ]);
        $dataProvider = $searchModel->search(\Yii::$app->request->get(),$pagination);

        return $this->render('complaints',[
            'searchModel'=>$searchModel,
            'dataProvider'=>$dataProvider
        ]);
    }

    public function actionActComplaints(){


        $type = \Yii::$app->request->get('type');
        $entities_id = \Yii::$app->request->get('entities_id');
        $user_id = \Yii::$app->request->get('user_id');

        $modelName =  Complaints::getModelByType($type);
        $act = \Yii::$app->request->get('act');

        if(in_array($act,['delete'])){

            $model = $modelName::find()->where(['id'=>$entities_id])->one();
            if($model){
                $model->delete();
            }

        }


         Complaints::updateAll(['status'=>Complaints::$VERIFIED_STATUS],
            ['entities_id'=>$entities_id,'user_id'=>$user_id,'type'=>$type]);

        $searchModel = new ComplaintsSearch();
        $pagination = new Pagination([
            'pageSize' => \Yii::$app->request->get('per-page', 8),
            'page' => \Yii::$app->request->get('page', 1)-1,
            'route'=>'/admin/moderation/complaints',
            'selfParams'=>[
                'sort'=>true,
            ]
        ]);

        $dataProvider = $searchModel->search(\Yii::$app->request->get(),$pagination);

        return $this->render('complaints',[
            'searchModel'=>$searchModel,
            'dataProvider'=>$dataProvider
        ]);

    }

    public function actionPhoto(){

        $searchModel = new GallerySearch();

        $pagination = new Pagination([
            'pageSize' => \Yii::$app->request->get('per-page', 8),
            'page' => \Yii::$app->request->get('page', 1)-1,
            'route'=>'/admin/moderation/photo',
            'selfParams'=>[
                'sort'=>true,
            ]
        ]);
        $dataProvider = $searchModel->search(\Yii::$app->request->get(),$pagination);

      return  $this->render('photo',['dataProvider'=>$dataProvider]);
    }

    public function actionActPhoto(){
        $act = \Yii::$app->request->get('act');
        $id = \Yii::$app->request->get('id');
        $photo = Gallery::find()->where(['id'=>$id])->one();

        if(in_array($act,['delete','confirm','confirm2']) && $photo){


            switch ($act) {
                case 'delete': {
                    $photo->delete();
                }
                    break;
                case 'confirm': {
                    $photo->status = Gallery::$STATUS['confirm'];
                    $photo->save();
                }
                    break;
                case 'confirm2': {
                    $photo->status = Gallery::$STATUS['confirm'];
                    if($photo->save()){

                        $link = $photo->getLink();
                        $titlePost = $photo->post->data;

                        $templateMessage = \Yii::$app->params['notificationTemplates']['reward.photo'];
                        $message = sprintf($templateMessage['confirm'], $link, 2, 0.02,$titlePost);
                        $messageEmail = sprintf($templateMessage['emailConfirm'], 2, 0.02,$titlePost);

                        UserHelper::chargeBonuses($photo->user_id,2,0.02,
                            $message,$messageEmail,$link);
                    }
                }
                    break;
            }

        }


        $searchModel = new GallerySearch();

        $pagination = new Pagination([
            'pageSize' => \Yii::$app->request->get('per-page', 8),
            'page' => \Yii::$app->request->get('page', 1)-1,
            'route'=>'/admin/moderation/photo',
            'selfParams'=>[
                'sort'=>true,
            ]
        ]);
        $dataProvider = $searchModel->search(\Yii::$app->request->get(),$pagination);

        return  $this->render('photo',['dataProvider'=>$dataProvider]);

    }

    public function actionReviews(){

        $searchModel = new ReviewsSearch();


        $pagination = new Pagination([
            'pageSize' => \Yii::$app->request->get('per-page', 8),
            'page' => \Yii::$app->request->get('page', 1)-1,
            'route'=>'/admin/moderation/reviews',
        ]);

        $dataProvider = $searchModel->search(\Yii::$app->request->get(),$pagination);


        return  $this->render('reviews',['dataProvider'=>$dataProvider]);
    }

    public function actionActReviews(){
        $act = \Yii::$app->request->get('act');
        $id = \Yii::$app->request->get('id');

        $reviews = Reviews::find()->where(['id'=>$id])->one();

        if(in_array($act,['delete','confirm','confirm10','confirm12','confirm15']) && $reviews){


            switch ($act) {
                case 'delete': {
                    $reviews->delete();
                }
                    break;
                case 'confirm': {
                    $reviews->status = Reviews::$STATUS['confirm'];
                    $reviews->save();
                }
                    break;
                case 'confirm10': {
                    $reviews->status = Reviews::$STATUS['confirm'];
                    $reviews->is_accrue = Reviews::$IS_ACCRUE['true'];
                    if($reviews->save()){
                        $link = $reviews->getLink();
                        $titlePost = $reviews->post->data;

                        $templateMessage = \Yii::$app->params['notificationTemplates']['reward.reviews'];
                        $message = sprintf($templateMessage['confirm10'], $link, 10, 0.10,$titlePost);
                        $messageEmail = sprintf($templateMessage['emailConfirm10'], 10, 0.10,$titlePost);
                        UserHelper::chargeBonuses($reviews->user_id,10,0.10,
                            $message,$messageEmail,$link);
                    }
                }
                    break;
                case 'confirm12': {
                    $reviews->status = Reviews::$STATUS['confirm'];
                    $reviews->is_accrue = Reviews::$IS_ACCRUE['true'];
                    if($reviews->save()){
                        $link = $reviews->getLink();
                        $titlePost = $reviews->post->data;

                        $templateMessage = \Yii::$app->params['notificationTemplates']['reward.reviews'];
                        $message = sprintf($templateMessage['confirm12'], $link, 12, 0.12,$titlePost);
                        $messageEmail = sprintf($templateMessage['emailConfirm12'], 12, 0.12,$titlePost);

                        UserHelper::chargeBonuses($reviews->user_id,12,0.12,
                            $message,$messageEmail,$link);
                    }
                }
                    break;
                case 'confirm15': {
                    $reviews->status = Reviews::$STATUS['confirm'];
                    $reviews->is_accrue = Reviews::$IS_ACCRUE['true'];
                    if($reviews->save()){
                        $link = $reviews->getLink();
                        $titlePost = $reviews->post->data;

                        $templateMessage = \Yii::$app->params['notificationTemplates']['reward.reviews'];
                        $message = sprintf($templateMessage['confirm15'], $link, 15, 0.15,$titlePost);

                        $messageEmail = sprintf($templateMessage['emailConfirm15'], 15, 0.15,$titlePost);

                        UserHelper::chargeBonuses($reviews->user_id,15,0.15,
                            $message,$messageEmail,$link);
                    }
                }
                    break;
            }

        }

        $searchModel = new ReviewsSearch();
        $pagination = new Pagination([
            'pageSize' => \Yii::$app->request->get('per-page', 8),
            'page' => \Yii::$app->request->get('page', 1)-1,
            'route'=>'/admin/moderation/reviews',
        ]);

        $dataProvider = $searchModel->search(\Yii::$app->request->get(),$pagination);


        return  $this->render('reviews',['dataProvider'=>$dataProvider]);

    }

    public function actionCancelsReviews(){
        $response = new \stdClass();
        $response->success = true;
        $response->message = 'Отзыв успешно скрыт';

        $message = \Yii::$app->request->post('message',false);
        $id = \Yii::$app->request->post('id',false);

        if ($message && $id) {
            $reviews = Reviews::find()->where(['id' => $id])->one();
            if ($reviews) {

                $reviews->status = Reviews::$STATUS['private'];
                if($reviews->save()){
                    $link = $reviews->getLink();

                    $templateMessage = \Yii::$app->params['notificationTemplates']['reviews'];
                    $messageNotice = sprintf($templateMessage['cancels'], $link,$message);
                    $emailMessage = sprintf($templateMessage['emailCancels'],$message);

                    UserHelper::sendNotification($reviews->user_id,[
                        'type' => '',
                        'data' => $messageNotice
                    ]);

                    UserHelper::sendMessageToEmailCustomReward($reviews->user,$emailMessage,$link);

                }

            }
        } else {
            $response->success = false;
            $response->message = 'Введите текст сообщения';
        }

        return $this->asJson($response);

    }

    public function actionGetFormCancels(){
        return $this->renderAjax('__cancels');
    }


    public function actionPost(){

        $searchModel = new PostsModerationSearch();


        $pagination = new Pagination([
            'pageSize' => \Yii::$app->request->get('per-page', 8),
            'page' => \Yii::$app->request->get('page', 1)-1,
            'route'=>'/admin/moderation/post',
        ]);

        $dataProvider = $searchModel->search(\Yii::$app->request->get(),$pagination);


        return $this->render('list_post',[
            'searchModel'=>$searchModel,
            'dataProvider'=>$dataProvider
        ]);
    }

    public function actionActPost(){
        $id = \Yii::$app->request->get('id',false);
        $mainId = \Yii::$app->request->get('main_id',false);
        $act = \Yii::$app->request->get('act',false);

        if(in_array($act,['confirm','confirm10','delete'])){

            switch ($act) {

                case 'confirm': {

                    if(!$mainId){

                        $mainPost = Posts::find()->where(['id' => $id])->one();

                        if($mainPost){
                            $mainPost->status = Posts::$STATUS['confirm'];
                            $mainPost->save();
                        }

                        AddPost::updateCountUserPlace($mainPost->user_id);

                    }else{

                        $newPost = PostsModeration::find()->where(['id'=>$id])->one();
                        if($newPost->replacement($mainId)){
                            $newPost->delete();
                            AddPost::updateCountUserPlace($newPost->user_id);
                        }

                    }


                }
                    break;
                case 'confirm10': {

                    if(!$mainId){

                        $mainPost = Posts::find()->where(['id' => $id])->one();

                        if($mainPost){
                            $mainPost->status = Posts::$STATUS['confirm'];
                            if($mainPost->save()){
                                AddPost::updateCountUserPlace($mainPost->user_id);

                                $link = '/'.$mainPost->url_name.'-p'.$mainPost->id;
                                $titlePost = $mainPost->data;
                                $templateMessage = \Yii::$app->params['notificationTemplates']['post'];
                                $message = sprintf($templateMessage['confirm'], $link, 10, 0.10,$titlePost);
                                $messageEmail = sprintf($templateMessage['emailConfirm'], 10, 0.10,$titlePost);
                                UserHelper::chargeBonuses($mainPost->user_id,10,0.10,
                                    $message,$messageEmail,$link);

                            }
                        }

                    }else{

                        $newPost = PostsModeration::find()->where(['id'=>$id])->one();
                        if($newPost->replacement($mainId)){

                            $mainPost = Posts::find()->where(['id' => $mainId])->one();
                            $link = '/'.$mainPost->url_name.'-p'.$mainPost->id;
                            $titlePost = $mainPost->data;
                            $templateMessage = \Yii::$app->params['notificationTemplates']['post'];
                            $message = sprintf($templateMessage['confirmEdit'], $link, 5, 0.05,$titlePost);
                            $messageEmail = sprintf($templateMessage['emailConfirmEdit'], 5, 0.05,$titlePost);
                            UserHelper::chargeBonuses($newPost->user_id,5,0.05,
                                $message,$messageEmail,$link);

                            $newPost->delete();
                            AddPost::updateCountUserPlace($newPost->user_id);

                        }

                    }

                }
                    break;
                case 'delete': {

                    if(!$mainId){

                        $mainPost = Posts::find()->where(['id' => $id])->one();

                        if($mainPost){
                            $mainPost->delete();
                            AddPost::updateCountUserPlace($mainPost->user_id);
                        }



                    }else{

                        $post = PostsModeration::find()
                            ->where(['main_id' => $mainId])
                            ->andWhere(['id' => $id])
                            ->one();

                        if($post){
                            $post->delete();
                            AddPost::updateCountUserPlace($post->user_id);
                        }

                    }

                }
                    break;
            }
        }

        $searchModel = new PostsModerationSearch();


        $pagination = new Pagination([
            'pageSize' => \Yii::$app->request->get('per-page', 8),
            'page' => \Yii::$app->request->get('page', 1)-1,
            'route'=>'/admin/moderation/post',
        ]);

        $dataProvider = $searchModel->search(\Yii::$app->request->get(),$pagination);


        return $this->render('list_post',[
            'searchModel'=>$searchModel,
            'dataProvider'=>$dataProvider
        ]);

    }


    public function actionCancelsPost(){
        $response = new \stdClass();
        $response->success = true;
        $response->message = 'Место успешно скрыто';

        $message = \Yii::$app->request->post('message',false);
        $id = \Yii::$app->request->post('id',false);
        $mainId = \Yii::$app->request->post('main_id',false);

        if ($message && $id) {

            if(!$mainId){

                $mainPost = Posts::find()->where(['id' => $id])->one();

                if($mainPost){
                    $mainPost->status = Posts::$STATUS['private'];
                    $mainPost->update();

                    $link = '/'.$mainPost->url_name.'-p'.$mainPost->id;
                    $titlePost = $mainPost->data;
                    $templateMessage = \Yii::$app->params['notificationTemplates']['post'];
                    $messageNotice = sprintf($templateMessage['cancel'], $link, $titlePost,$message);
                    $messageEmail = sprintf($templateMessage['emailCancel'], $titlePost,$message);

                    UserHelper::sendNotification($mainPost->user_id, [
                        'type' => '',
                        'data' => $messageNotice,
                    ]);
                    UserHelper::sendMessageToEmailCustomReward($mainPost->user,$messageEmail,$link);
                    AddPost::updateCountUserPlace($mainPost->user_id);
                }

            }else{

                $post = PostsModeration::find()
                    ->where(['main_id' => $mainId])
                    ->andWhere(['id' => $id])
                    ->one();

                if($post){
                    $post->status = PostsModeration::$STATUS['private'];
                    $post->update();


                    $link = '/'.$post->url_name.'-p'.$post->main_id.'/moderation';
                    $titlePost = $post->data;
                    $templateMessage = \Yii::$app->params['notificationTemplates']['post'];
                    $messageNotice = sprintf($templateMessage['cancel'], $link, $titlePost,$message);
                    $messageEmail = sprintf($templateMessage['emailCancel'], $titlePost,$message);

                    UserHelper::sendNotification($post->user_id, [
                        'type' => '',
                        'data' => $messageNotice,
                    ]);
                    UserHelper::sendMessageToEmailCustomReward($post->user,$messageEmail,$link);
                    AddPost::updateCountUserPlace($post->user_id);

                }
            }


        } else {
            $response->success = false;
            $response->message = 'Введите текст сообщения';
        }

        return $this->asJson($response);

    }



}
