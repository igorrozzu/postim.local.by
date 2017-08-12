<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Category;
use app\models\City;
use app\models\Discounts;
use app\models\entities\DiscountOrder;
use app\models\Notification;
use app\models\Posts;
use app\models\Region;
use app\models\Reviews;
use app\models\TotalView;
use app\models\UnderCategory;
use yii\console\Controller;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class TestDataController extends Controller
{

    public function actionDiscountOrder($userId, $discountId, $pincode, $status, $count)
    {
        for ($i = 0; $i < $count; $i++) {
            $model = new DiscountOrder([
                'user_id' => (int)$userId,
                'discount_id' => (int)$discountId,
                'date_buy' => time(),
                'date_finish' => time() + 3600 * 12,
                'promo_code' => '123 89',
                'status_promo' => (int)$status,
                'pin_code' => (int)$pincode === -1 ? null : (int)$pincode,
            ]);

            if ($model->validate() && $model->save()) {
                echo 'row ' . $model->id . " была сохранена\n\r";
            } else {
                var_dump($model->getErrors());
            }
        }

    }

    public function actionDiscounts($postId, $totalViewId, $status, $type, $count)
    {
        for ($i = 0; $i < $count; $i++) {
            $model = new Discounts([
                'post_id' => (int)$postId,
                'data' => 'Test data for discounts',
                'header' => 'Скидка 50% на букеты цветов, из бабушкеной каллекции, а также скидка 3% за каждую новую покупку.',
                'cover' => 'testP.png',
                'price' => 123456,
                'status' => (int)$status,
                'number_purchases' => 12,
                'price_promo' => 123456,
                'discount' => 123456,
                'total_view_id' => (int)$totalViewId,
                'date_start' => time(),
                'date_finish' => time() + 3600 * 12,
                'type' => (int)$type,
            ]);

            if ($model->validate() && $model->save()) {
                echo 'row ' . $model->id . " была сохранена\n\r";
            } else {
                var_dump($model->getErrors());
            }
        }
    }

    public function actionTotalView()
    {
        $model = new TotalView([
            'count' => 12345,
        ]);

        if ($model->validate() && $model->save()) {
            echo 'row ' . $model->id . " была сохранена\n\r";
        } else {
            echo 'row ' . $model->id . " не была сохранена\n\r";
        }
    }





}
