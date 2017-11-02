<?php

namespace app\modules\admin\controllers;

use app\models\Reviews;
use app\modules\admin\components\AdminDefaultController;
use app\modules\admin\models\Complaints;
use app\modules\admin\models\ComplaintsSearch;
use app\components\Pagination;
use app\modules\admin\models\Gallery;
use app\modules\admin\models\GallerySearch;
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

        if(in_array($act,['delete','confirm','cancels'])){

            switch ($act){

                case 'delete': {

                    $model = $modelName::find()->where(['id'=>$entities_id])->one();
                    if($model){
                        $model->delete();
                    }

                };
                    break;

                case 'cancels': {

                    $model = $modelName::find()->where(['entities_id'=>$entities_id])->one();
                    if($model){
                        $model->status = Reviews::$STATUS['private'];
                        $model->save();
                    }

                };
                    break;
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

        if(in_array($act,['delete','confirm']) && $photo){


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



}
