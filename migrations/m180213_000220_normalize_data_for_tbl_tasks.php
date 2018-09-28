<?php

use app\models\entities\DiscountOrder;
use app\repositories\TaskRepository;
use yii\db\Migration;
use yii\helpers\Url;

class m180213_000220_normalize_data_for_tbl_tasks extends Migration
{
    public function safeUp()
    {
        $orders = DiscountOrder::find()
            ->innerJoinWith(['discount.post', 'user'])
            ->all();

        foreach ($orders as $order) {

            TaskRepository::addMailTask('SendMessageToEmail', [
                'htmlLayout' => 'layouts/default',
                'view' => ['html' => 'reviewAboutDiscount'],
                'params' => [
                    'userName' => $order->user->name,
                    'discountTitle' => $order->discount->header,
                    'postTitle' => $order->discount->post->data,
                    'postUrl' =>
                        Yii::$app->urlManager->createAbsoluteUrl([
                            'post/index',
                            'url' => $order->discount->post->url_name,
                            'id' => $order->discount->post->id,
                        ]),
                ],
                'toEmail' => $order->user->email,
                'subject' => "{$order->user->name}, оставьте отзыв о {$order->discount->post->data} на Postim.by",
            ], $order->date_finish);
        }
    }

    public function safeDown()
    {
        echo "m180213_000220_normalize_data_for_tbl_tasks cannot be reverted.\n";

        return false;
    }
}
