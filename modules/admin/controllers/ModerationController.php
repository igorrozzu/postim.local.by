<?php

namespace app\modules\admin\controllers;

use app\modules\admin\components\AdminDefaultController;
use app\modules\admin\models\Complaints;
use app\modules\admin\models\ComplaintsSearch;
use yii\data\Pagination;
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
        $dataProvider = $searchModel->search(\Yii::$app->request->get());

        return $this->render('complaints',[
            'searchModel'=>$searchModel,
            'dataProvider'=>$dataProvider
        ]);
    }

    public function actionActComplaints(){

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
