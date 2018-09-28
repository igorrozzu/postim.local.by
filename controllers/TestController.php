<?php

namespace app\controllers;

use app\components\MainController;
use app\components\UserHelper;
use app\models\OrdersPromo;
use app\models\User;

class TestController extends MainController
{
    public function actionIndex()
    {
        return $this->render('test-discount');
    }


    public function actionGainPromo()
    {

        $response = new \stdClass();
        $response->error = false;

        if (!\Yii::$app->user->isGuest) {
            $userOrderPromo = new OrdersPromo();
            $userOrderPromo->user_id = \Yii::$app->getUser()->getId();

            if ($userOrderPromo->save()) {
                $user = User::findOne(['id' => \Yii::$app->getUser()->getId()]);
                $message = 'Для получения скидки сообщите до заказа промокод «Postim2017»';

                UserHelper::sendMessageToEmail($user, $message, '/Sushi-sety-rolly-ot-sluzhby-dostavki-Sushi-Nashi');
            }

            $response->message = 'Промокод был выслан на электронную почту.';
            return $this->asJson($response);

        } else {
            $response->error = true;
            $response->message = 'Незарегистрированные пользователи не могут получить скидку';
            return $this->asJson($response);

        }

    }

}
