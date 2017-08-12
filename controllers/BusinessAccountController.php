<?php
/**
 * Created by PhpStorm.
 * User: igorrozu
 * Date: 8/10/17
 * Time: 10:51 PM
 */

namespace app\controllers;


use app\components\AuthController;
use app\models\Discounts;
use app\models\entities\DiscountOrder;
use yii\db\Exception;

class BusinessAccountController extends AuthController
{

    public function actionConfirmSuccessOrder()
    {
        $request = \Yii::$app->request;
        $id = $request->post('id');
        $code = $request->post('code');
        $condition = ['id' => $id, 'status_promo' => 1];
        if(isset($code)) {
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
}