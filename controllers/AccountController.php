<?php
/**
 * Created by PhpStorm.
 * User: igorrozu
 * Date: 8/10/17
 * Time: 10:51 PM
 */

namespace app\controllers;


use app\components\AuthController;
use app\components\orderStatisticsWidget\OrderStatisticsWidget;
use app\components\Pagination;
use app\models\Discounts;
use app\models\entities\BusinessOrder;
use app\models\entities\DiscountOrder;
use app\models\entities\OwnerPost;
use app\models\forms\PremiumAccount;
use app\models\payment\AccountPayment;
use app\models\Posts;
use app\models\search\AccountHistorySearch;
use app\models\search\DiscountOrderSearch;
use app\widgets\accountStatistic\AccountStatistic;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Exception;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

class AccountController extends AuthController
{
    public function actionHistory()
    {
        $request = Yii::$app->request;

        $historySearch = new AccountHistorySearch();
        $pagination = new Pagination([
            'pageSize' => $request->get('per-page', 8),
            'page' => $request->get('page', 1) - 1,
        ]);
        $loadTime = $request->get('loadTime', time());

        $dataProvider = $historySearch->statisticsSearch(
            $request->queryParams,
            $pagination,
            $loadTime
        );

        if ($request->isAjax && !$request->get('_pjax',false)) {
            return AccountStatistic::widget([
                'dataProvider' => $dataProvider,
                'settings' => [
                    'show-more-btn' => true,
                    'replace-container-id' => 'item-table-statistic',
                    'load-time' => $loadTime,
                ]
            ]);
        }

        $breadcrumbParams = $this->getParamsForBreadcrumb();
        $breadcrumbParams[] = [
            'name' => 'История вашего счета',
            'url_name' => Url::to(['user/history']),
            'pjax' => 'class="main-pjax a"'
        ];

        return $this->render('account-history', [
            'userInfo' => Yii::$app->user->identity->userInfo,
            'breadcrumbParams' => $breadcrumbParams,
            'dataProvider' => $dataProvider,
            'loadTime' => $loadTime,
        ]);
    }

    public function actionPremium()
    {
        $breadcrumbParams = $this->getParamsForBreadcrumb();
        $breadcrumbParams[] = [
            'name' => 'Премиум аккаунт',
            'url_name' => Url::to(['account/premium']),
            'pjax' => 'class="main-pjax a"'
        ];

        $errors = [];

        if (Yii::$app->request->isPost) {
            $model = new PremiumAccount();
            $model->load(Yii::$app->request->post(), 'premium-account');

            if ($model->validate()) {

                $rateInfo = $model->getRateInfo();
                $userInfo = Yii::$app->user->identity->userInfo;

                if ($userInfo->virtual_money - $rateInfo['cost'] < 0) {
                    Yii::$app->session->setFlash('message', [
                        'type' => 'error',
                        'text' => 'Недостаточно средств на счете',
                    ]);
                    return $this->redirect(Url::to(['account/replenishment']));
                }

                $account = BusinessOrder::find()
                    ->where([
                        'user_id' => Yii::$app->user->getId(),
                        'post_id' => $model->postId,
                    ])->one();

                if (!$account) {
                    Yii::$app->session->setFlash('message', [
                        'type' => 'error',
                        'text' => 'Бизнесс аккаунт не найден',
                    ]);
                    return $this->redirect(Url::to(['account/premium']));
                }

                $transaction = Yii::$app->db->beginTransaction();
                $result = $userInfo->updateCounters([
                    'virtual_money' => -$rateInfo['cost']
                ]);

                $time = time();
                $period = $rateInfo['duration'] * 24 * 3600;

                if ($account->premium_finish_date <= $time) {
                    $account->premium_finish_date = $time + $period;
                } else {
                    $account->premium_finish_date += $period;
                }
                $account->status = BusinessOrder::$PREMIUM_BIZ_AC;

                $result = $result && $account->update();

                if ($result) {
                    $transaction->commit();

                    Yii::$app->session->setFlash('message', [
                        'type' => 'success',
                        'text' => 'Бизнесс аккаунт подключен на ' . $rateInfo['duration'] . ' дней',
                    ]);

                } else {
                    $transaction->rollBack();
                }

            } else {
                $errors = array_values($model->getFirstErrors());
            }
        }

        $posts = Posts::find()
            ->innerJoinWith(['ownersPost' => function (ActiveQuery $q) {
                $q->onCondition([OwnerPost::tableName() . '.owner_id' => Yii::$app->user->id]);
            }])
            ->orderBy(['data' => SORT_ASC])
            ->all();

        return $this->render('premium-account', [
            'breadcrumbParams' => $breadcrumbParams,
            'posts' => $posts,
            'errors' => $errors,
        ]);
    }

    public function actionReplenishment()
    {
        $model = new AccountPayment();

        $breadcrumbParams = $this->getParamsForBreadcrumb();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post(), 'payment');
            $model->load(['user_id' =>Yii::$app->user->identity->getId()], '');

            if ($model->validate() && $model->save()) {

                $breadcrumbParams[] = [
                    'name' => 'Оплата через систему "Расчет" (ЕРИП)',
                    'url_name' => Url::to(['user/account']),
                    'pjax' => 'class="main-pjax a"'
                ];

                return $this->render('erip-payment', [
                    'model' => $model,
                    'breadcrumbParams' => $breadcrumbParams
                ]);
            }
        }

        $userInfo = Yii::$app->user->identity->userInfo;

        $breadcrumbParams[] = [
            'name' => 'Пополнение счета',
            'url_name' => Url::to(['user/account']),
            'pjax' => 'class="main-pjax a"'
        ];

        return $this->render('account', [
            'userInfo' => $userInfo,
            'model' => $model,
            'breadcrumbParams' => $breadcrumbParams,
            'errors' => array_values($model->getFirstErrors()),
        ]);
    }

    public function actionPayment()
    {
        return $this->render('erip-payment');
    }

    public function actionOrderPromocodes()
    {
        if (!Yii::$app->user->isOwnerPost()) {
            throw new NotFoundHttpException('Cтраница не найдена');
        }

        $searchModel = new DiscountOrderSearch();
        $request = Yii::$app->request;
        $pagination = new Pagination([
            'pageSize' => $request->get('per-page', 8),
            'page' => $request->get('page', 1) - 1,
            'selfParams'=> [
                'type' => true,
                'order_time' => true,
                'promo_code' => true,
            ],
        ]);
        $loadTime = $request->get('loadTime', time());
        $_GET['type'] = $_GET['type'] ?? 'promocode';
        $dataProvider = $searchModel->statisticsSearch(
            $request->queryParams,
            $pagination,
            $loadTime
        );

        if($request->isAjax && !$request->get('_pjax',false)) {
            return OrderStatisticsWidget::widget([
                'dataProvider' => $dataProvider,
                'settings' => [
                    'show-more-btn' => true,
                    'replace-container-id' => 'feed-promo',
                    'load-time' => $loadTime,
                    'view-name' => $request->get('only_rows', false) ? 'rows' : 'index',
                    'column-status-view' => 'promocode',
                    'time-range' => $searchModel->getTimeRange(),
                ]
            ]);
        } else {
            $breadcrumbParams = $this->getParamsForBreadcrumb();
            $breadcrumbParams[] = [
                'name' => 'Заказы промокодов',
                'url_name' => Url::to(['user/order-promocodes']),
                'pjax' => 'class="main-pjax a"'
            ];

            return $this->render('statistics-promo', [
                'breadcrumbParams' => $breadcrumbParams,
                'dataProvider' => $dataProvider,
                'loadTime' => $loadTime,
                'order_time' => $request->queryParams['order_time'] ?? null,
                'timeRange' => $searchModel->getTimeRange(),
            ]);
        }
    }

    public function actionConfirmSuccessOrder()
    {
        $request = \Yii::$app->request;
        $id = $request->post('id');
        $code = $request->post('code');
        $condition = ['id' => $id, 'status_promo' => 1];
        if (isset($code)) {
            $condition['pin_code'] = (int)$code;
        }
        $response = new \stdClass();
        $response->success = true;
        try {
            $count = DiscountOrder::updateAll([
                'status_promo' => 0,
            ], $condition);
            if ($count === 0) {
                $response->success = false;
            }
        } catch (Exception $e) {
            $response->success = false;
        }
        return $this->asJson($response);
    }

    private function getParamsForBreadcrumb()
    {
        $breadcrumbParams=[];

        $currentUrl = Yii::$app->getRequest()->getHostInfo();
        $breadcrumbParams[] = [
            'name' => ucfirst(Yii::$app->getRequest()->serverName),
            'url_name' => $currentUrl,
            'pjax' => 'class="main-header-pjax a"'
        ];

        if($city = Yii::$app->city->getSelected_city()){
            if($city['url_name']){
                $currentUrl=$currentUrl.'/'.$city['url_name'];
                $breadcrumbParams[]=[
                    'name'=>$city['name'],
                    'url_name'=>$currentUrl,
                    'pjax'=>'class="main-pjax a"'
                ];
            }
        }

        return $breadcrumbParams;
    }
}