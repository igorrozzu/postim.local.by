<?php
namespace app\repositories;


use app\models\Discounts;

class DiscountRepository extends Discounts
{
    public static function getVisibleCountByPostId(int $id)
    {
        return self::find()
            ->where(['post_id' => $id])
            ->andWhere(['>=', self::tableName() . '.status', self::STATUS['active']])
            ->count();
    }
}