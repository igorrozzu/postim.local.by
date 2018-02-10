<?php

namespace app\commands\cron\notifications;

use app\models\entities\DiscountOrder;
use Yii;

class NewPromocode extends BaseCronNotificationHandler
{
    public function run()
    {
        $order = DiscountOrder::find()
            ->innerJoinWith(['user', 'discount.post.city', 'discount.post.info'])
            ->where([DiscountOrder::tableName() . '.id' => $this->params->order_id])
            ->one();

        $mailer = Yii::$app->getMailer();
        $mailer->htmlLayout = 'layouts/default';

        $mailer->compose(['html' => 'promocode'], [
            'discount' => $order->discount,
            'discountOrder' => $order,
        ])->setFrom([Yii::$app->params['mail.supportEmail'] => 'Postim.by'])
            ->setTo($order->user->email)
            ->setSubject('Ваш промокод от Postim.by')
            ->send();
    }
}