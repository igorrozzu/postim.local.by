<?php

namespace app\modules\admin\controllers;

use app\models\City;
use app\modules\admin\components\AdminDefaultController;
use app\modules\admin\models\BusinessOrder;
use app\modules\admin\models\BusinessOrderSearch;
use app\modules\admin\models\News;
use app\modules\admin\models\OwnerPost;
use yii\data\Pagination;
use yii\web\Controller;

/**
 * Default controller for the `admin` module
 */
class BizController extends AdminDefaultController
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $biz = new OwnerPost();
        $biz_account = new BusinessOrder();

        $searchModel = new BusinessOrderSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->get(),BusinessOrder::$BIZ_AC);
        $dataProviderOrder = $searchModel->search(\Yii::$app->request->get(),BusinessOrder::$BIZ_ORDER);

        return $this->render('index',[
            'biz'=>$biz,
            'biz_account'=>$biz_account,
            'dataProvider' => $dataProvider,
            'dataProviderOrder' => $dataProviderOrder,
            'searchModel' => $searchModel,
        ]);

    }


    public function actionSave(){

        if (\Yii::$app->request->isPost) {

            $biz = new OwnerPost();
            $biz_account = new BusinessOrder();
            if ($biz->load(\Yii::$app->request->post()) &&
                $biz_account->load(\Yii::$app->request->post())) {

                $biz_account->status = BusinessOrder::$BIZ_AC;
                $biz_account->user_id = $biz->owner_id;
                $biz_account->post_id = $biz->post_id;

                $transaction = \Yii::$app->db->beginTransaction();

                if ($biz->save() && $biz_account->save()) {

                    $transaction->commit();


                    $toastMessage = [
                        'type' => 'success',
                        'message' => 'Пользователь добавлен',
                    ];

                    \Yii::$app->session->setFlash('toastMessage',$toastMessage);

                    return $this->redirect('/admin/biz');

                } else {
                    $transaction->rollBack();

                    $searchModel = new BusinessOrderSearch();
                    $dataProvider = $searchModel->search(\Yii::$app->request->get(), BusinessOrder::$BIZ_AC);
                    $dataProviderOrder = $searchModel->search(\Yii::$app->request->get(), BusinessOrder::$BIZ_ORDER);

                    $params = [
                        'dataProvider' => $dataProvider,
                        'dataProviderOrder' => $dataProviderOrder,
                        'searchModel' => $searchModel
                    ];

                }
            }
            $params['biz'] = $biz;
            $params['biz_account'] = $biz_account;

            return $this->render('index', $params);
        }
    }

    public function actionChangeStatus(){
        if(\Yii::$app->request->isPost){
            $request =\Yii::$app->request;

            $biz_account =  BusinessOrder::find()->where([
                'user_id'=>$request->post('user_id',null),
                'post_id'=>$request->post('post_id',null),
            ])->one();

            switch ($request->post('action',null)){
                case 'remove':{

                    $biz = OwnerPost::find()->where([
                        'owner_id'=>$request->post('user_id',null),
                        'post_id'=>$request->post('post_id',null),
                    ])->one();

                    if($biz){
                        $biz->delete();
                    }

                    if($biz_account){
                        if($biz_account->status == BusinessOrder::$BIZ_ORDER){
                            $biz_account->delete();
                        }else{
                            $biz_account->status = BusinessOrder::$BIZ_ORDER;
                            $biz_account->update();
                        }
                    }

                }break;
                case 'confirm':{
                    $biz_account->status = BusinessOrder::$BIZ_AC;
                    $biz_account->date = time();
                    $biz_account->update();
                    $biz = new OwnerPost([
                        'owner_id' => $biz_account->user_id,
                        'post_id' => $biz_account->post_id
                    ]);
                    $biz->save();

                }break;
            }

            return $this->asJson(['success'=>true,'action'=>'remove']);

        }
    }



}
