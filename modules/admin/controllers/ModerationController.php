<?php

namespace app\modules\admin\controllers;

use app\models\Reviews;
use app\modules\admin\components\AdminDefaultController;
use app\modules\admin\models\Complaints;
use app\modules\admin\models\ComplaintsSearch;
use app\components\Pagination;
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

    public function actionChangeStatus(){

        if(\Yii::$app->request->isPost){
            $request =\Yii::$app->request;

            $complaints =  Complaints::find()->where([
                'user_id'=>$request->post('user_id',null),
                'entities_id'=>$request->post('entities_id',null),
                'type'=>$request->post('type',null),
            ])->one();

            switch ($request->post('action',null)){
                case Complaints::$VERIFIED_STATUS:{


                    if($complaints){
                        $complaints->status = Complaints::$VERIFIED_STATUS;
                        $complaints->update();
                    }


                }break;
                case Complaints::$MODERATION_STATUS:{
                    if($complaints){
                        $complaints->status = Complaints::$MODERATION_STATUS;
                        $complaints->update();
                    }

                }break;
            }

            return $this->asJson(['success'=>true,'action'=>'update']);

        }

    }



}
