<?php

namespace app\components\discountOrder;

use app\models\Discounts;
use app\models\forms\DiscountOrder;
use Yii;
use yii\db\Exception;

/**
 * Created by PhpStorm.
 * User: igorrozu
 * Date: 12/25/17
 * Time: 3:40 PM
 */
class PromoCodeOrder extends Order
{
    public function createOrder(): bool
    {
        try {
            $transaction = $this->userInfo->getDb()->beginTransaction();
            $this->totalCost = $this->orderForm->count * $this->orderForm->discount->price_promo;

            if (!$this->makePayment()) {
                $transaction->rollback();
                return false;
            }

            $orders = [];
            $order = [
                'user_id' => Yii::$app->user->id,
                'discount_id' => $this->orderForm->discount->id,
                'date_buy' => time(),
                'date_finish' => $this->orderForm->discount->date_finish,
                'promo_code' => null,
                'pin_code' => null,
                'status_promo' => Discounts::STATUS['active'],
            ];

            for ($i = 0; $i < $this->orderForm->count; $i++) {
                $order['promo_code'] = Yii::$app->security->generateRandomString(10);
                $orders[] = $order;
            }

            Yii::$app->db->createCommand()
                ->batchInsert(\app\models\entities\DiscountOrder::tableName(),
                    ['user_id', 'discount_id', 'date_buy', 'date_finish', 'promo_code', 'pin_code', 'status_promo'], $orders)
                ->execute();
        } catch (Exception $e) {
            $transaction->rollback();
            return false;
        }

        $transaction->commit();
        $this->renderView = 'success-order';

        return true;
    }

    protected function paymentByErip(): bool
    {
        // TODO: Implement paymentByErip() method.
    }

    protected function paymentByCard(): bool
    {
        // TODO: Implement paymentByCard() method.
    }

    protected function paymentByRealMoney(): bool
    {
        // TODO: Implement paymentByRealMoney() method.
    }

    protected function paymentByMegaMoney(): bool
    {
        if ($this->totalCost > $this->userInfo->mega_money) {
            $this->renderView = 'basket-lack-of-mega-money';
            return false;
        }

        $this->userInfo->updateCounters([
            'mega_money' => -$this->totalCost,
        ]);

        return true;
    }

    protected function makePayment(): bool
    {
        switch ((int) $this->orderForm->paymentType)
        {
            case DiscountOrder::PAYMENT_TYPE['mega-money']: return $this->paymentByMegaMoney();
            default: return false;
        }
    }

}