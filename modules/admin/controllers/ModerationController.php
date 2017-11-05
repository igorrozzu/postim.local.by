<?php

namespace app\modules\admin\controllers;

use app\components\UserHelper;
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



}
