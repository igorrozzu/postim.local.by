<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;
use app\models\entities\DiscountOrder;
use yii\console\Controller;



class FixController extends Controller
{
    public function actionFillDateFinish()
    {
        $orders = DiscountOrder::find()
            ->innerJoinWith(['discount'])
            ->where([DiscountOrder::tableName() . '.status_promo', DiscountOrder::STATUS['active']]);

        foreach ($orders as $order){
           $order->date_finish = $order->discount->date_finish;
           $order->save();
        }
    }
}
