<?php
/**
 * Created by PhpStorm.
 * User: igorrozu
 * Date: 8/10/17
 * Time: 10:51 PM
 */

namespace app\controllers;


use app\components\AuthController;
use app\components\MainController;
use app\components\orderStatisticsWidget\OrderStatisticsWidget;
use app\components\Pagination;
use app\models\Discounts;
use app\models\entities\BidBusinessOrder;
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

class LadingController extends MainController
{

    public function actionSaleOfABusinessAccount()
    {

        $this->layout = 'layoutLading';

        return $this->render('sale_of_business');
    }

    public function actionGetFormForOrderBsa()
    {
        $businessOrder = new BidBusinessOrder();
        $businessOrder->load(Yii::$app->request->get(),'');
        return $this->renderAjax('__form_add_business_account', ['businessOrder' => $businessOrder]);

    }

    public function actionSaveOrderBsa()
    {

        $response = new \stdClass();
        $response->html = '';
        $response->success = false;
        $response->message = '';

        $businessOrder = new BidBusinessOrder();
        $businessOrder->load(Yii::$app->request->post());

        if(!$businessOrder->save()){
            $response->html = $this->renderAjax('__form_add_business_account', ['businessOrder' => $businessOrder]);
            $response->message = 'Заполните корректно информацию';
        }else{
            $response->success = true;
            $response->message = 'Спасибо за вашу заявку. Она будет рассмотренав ближайшее время, после чего наш менеджер свяжется с вами';
        }

        return $this->asJson($response);

    }

}