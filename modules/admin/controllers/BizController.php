<?php

namespace app\modules\admin\controllers;

use app\components\UserHelper;
use app\models\City;
use app\models\entities\BidBusinessOrder;
use app\modules\admin\components\AdminDefaultController;
use app\modules\admin\models\SearchModels\BusinessBidSearch;
use app\modules\admin\models\BusinessOrder;
use app\modules\admin\models\BusinessOrderSearch;
use app\modules\admin\models\News;
use app\modules\admin\models\OwnerPost;
use app\repositories\BusinessOrderRepository;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

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
        $dataProviderPremiumAccount = $searchModel->searchPremiumAccounts(
            Yii::$app->request->get()
        );

        return $this->render('index',[
            'biz'=>$biz,
            'biz_account'=>$biz_account,
            'dataProvider' => $dataProvider,
            'dataProviderOrder' => $dataProviderOrder,
            'dataProviderPremiumAccount' => $dataProviderPremiumAccount,
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


                    $linkToPost = '/'.$biz_account->post->url_name.'-p'.$biz_account->post->id;
                    $titlePost = $biz_account->post->data;
                    $templateMessage = \Yii::$app->params['notificationTemplates']['biz_ac'];
                    $message = sprintf($templateMessage['confirm'], $linkToPost,$titlePost);
                    $emailMessage = sprintf($templateMessage['emailConfirm'],$titlePost);

                    UserHelper::sendNotification($biz_account->user_id,[
                        'type' => '',
                        'data' => $message
                    ]);

                    $user = $biz_account->user;
                    $user->name = $biz_account->full_name;

                    UserHelper::sendMessageToEmailCustomReward($user,$emailMessage,$linkToPost);


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
                    $dataProviderPremiumAccount = $searchModel->searchPremiumAccounts(
                        Yii::$app->request->get()
                    );
                    $params = [
                        'dataProvider' => $dataProvider,
                        'dataProviderOrder' => $dataProviderOrder,
                        'searchModel' => $searchModel,
                        'dataProviderPremiumAccount' => $dataProviderPremiumAccount
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

                            $linkToPost = '/'.$biz_account->post->url_name.'-p'.$biz_account->post->id;
                            $titlePost = $biz_account->post->data;
                            $templateMessage = \Yii::$app->params['notificationTemplates']['biz_ac'];
                            $message = sprintf($templateMessage['deActive'], $linkToPost,$titlePost);
                            $emailMessage = sprintf($templateMessage['emailDeActive'],$titlePost);

                            UserHelper::sendNotification($biz_account->user_id,[
                                'type' => '',
                                'data' => $message
                            ]);

                            $user = $biz_account->user;
                            $user->name = $biz_account->full_name;

                            UserHelper::sendMessageToEmailCustomReward($user,$emailMessage,$linkToPost);

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

                    if($biz->save()){

                        $linkToPost = '/'.$biz_account->post->url_name.'-p'.$biz_account->post->id;
                        $titlePost = $biz_account->post->data;
                        $templateMessage = \Yii::$app->params['notificationTemplates']['biz_ac'];
                        $message = sprintf($templateMessage['confirm'], $linkToPost,$titlePost);
                        $emailMessage = sprintf($templateMessage['emailConfirm'],$titlePost);

                        UserHelper::sendNotification($biz_account->user_id,[
                            'type' => '',
                            'data' => $message
                        ]);

                        $user = $biz_account->user;
                        $user->name = $biz_account->full_name;

                        UserHelper::sendMessageToEmailCustomReward($user,$emailMessage,$linkToPost);
                    }

                }break;
            }

            return $this->asJson(['success'=>true,'action'=>'remove']);

        }
    }

    public function actionAddBusinessAccount()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $post = $request->post('businessAccount');

            if (is_numeric($post['postId']) && is_numeric($post['userId'])
                && is_numeric($post['dayCount'])) {

                $account = BusinessOrderRepository::find()
                    ->where([
                        'user_id' => $post['userId'],
                        'post_id' => $post['postId'],
                    ])->one();

                if (!$account) {
                    Yii::$app->session->setFlash('toastMessage', [
                        'type' => 'error',
                        'message' => 'Бизнес-аккаунт не найден',
                    ]);
                    return $this->redirect($request->referrer);
                }

                $account->increasePremium($post['dayCount']);

                Yii::$app->session->setFlash('toastMessage', [
                    'type' => 'success',
                    'message' => 'Бизнес-аккаунт подключен на ' . $post['dayCount'] . ' дней',
                ]);
            } else {
                Yii::$app->session->setFlash('toastMessage', [
                    'type' => 'error',
                    'message' => 'Переданы неверные данные',
                ]);
            }

            return $this->redirect($request->referrer);
        } else {
            throw new NotFoundHttpException('Cтраница не найдена');
        }
    }

    public function actionChangeStatusBusiness()
    {
        if (Yii::$app->request->isPost) {
            $request = Yii::$app->request;

            $account =  BusinessOrder::find()->where([
                'user_id'=>$request->post('user_id',null),
                'post_id'=>$request->post('post_id',null),
            ])->one();

            switch ($request->post('action',null)) {
                case 'remove': {
                    $account->status = BusinessOrder::$BIZ_AC;
                    /*$linkToPost = '/'.$biz_account->post->url_name.'-p'.$biz_account->post->id;
                    $titlePost = $biz_account->post->data;
                    $templateMessage = \Yii::$app->params['notificationTemplates']['biz_ac'];
                    $message = sprintf($templateMessage['deActive'], $linkToPost,$titlePost);
                    $emailMessage = sprintf($templateMessage['emailDeActive'],$titlePost);

                    UserHelper::sendNotification($biz_account->user_id,[
                        'type' => '',
                        'data' => $message
                    ]);

                    $user = $biz_account->user;
                    $user->name = $biz_account->full_name;

                    UserHelper::sendMessageToEmailCustomReward($user,$emailMessage,$linkToPost);*/
                } break;
                case 'confirm': {
                    $account->status = BusinessOrder::$PREMIUM_BIZ_AC;
                } break;
            }

            $account->update();

            return $this->asJson([
                'success'=> true,
                'action' => 'remove',
            ]);
        }
    }

    public function actionOrders()
    {

        $searchModel = new BusinessBidSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->get());

        return $this->render('bid_oreder',[
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionChangeStatusOrder()
    {

        $id = Yii::$app->request->get( 'id', false );
        $action = Yii::$app->request->get( 'action', false );
        $order = BidBusinessOrder::find()->where( [ 'id' => $id ] )->one();


        if ( $order ) {

            switch ( $action ) {
                case 'delete':
                    {
                        $order->delete();
                    }
                    break;
                case 'confirm':
                    {
                        $order->status = BidBusinessOrder::$VERIFIED;
                        $order->save();
                    }
                    break;
            }

        }

        return $this->actionOrders();

    }


}
