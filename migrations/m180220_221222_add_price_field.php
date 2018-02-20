<?php

use app\models\entities\DiscountOrder;
use yii\db\Migration;

class m180220_221222_add_price_field extends Migration
{
    public function safeUp()
    {
        $this->execute('ALTER TABLE public.tbl_discount_order ADD price NUMERIC NULL;');

        $orders = DiscountOrder::find()
            ->innerJoinWith(['discount'])
            ->all();

        foreach ($orders as $order) {
            $order->price = $order->discount->price_with_discount ?? $order->discount->price;
            $order->save();
        }
    }

    public function safeDown()
    {
        $this->execute('ALTER TABLE public.tbl_discount_order DROP COLUMN price;');
    }
}
