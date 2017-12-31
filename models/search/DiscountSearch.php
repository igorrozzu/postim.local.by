<?php

namespace app\models\search;

use app\components\Pagination;
use app\models\Discounts;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;


class DiscountSearch extends Discounts
{
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param Pagination $pagination
     * @param int $loadTime
     *
     * @return ActiveDataProvider
     */
    public function searchByPost($params, Pagination $pagination, int $loadTime)
    {
        $query = self::find()
            ->where(['post_id' => $params['postId']])
            ->andWhere(['<=', 'date_start', $loadTime])
            ->andWhere(['status' => self::STATUS['active']])
            ->orderBy(['date_start' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,
        ]);

        return $dataProvider;
    }

    public function readDiscount(int $discountId): ? Model
    {
        $query = Discounts::find()
            ->innerJoinWith(['post', 'totalView'])
            ->joinWith(['gallery'])
            ->where([Discounts::tableName() . '.id' => $discountId]);

        return $query->one();
    }

    public function getDiscountsInModeration(Pagination $pagination)
    {
        $query = self::find()
            ->innerJoinWith(['user'])
            ->andWhere(['status' => self::STATUS['moderation']])
            ->orderBy(['date_start' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,
        ]);

        return $dataProvider;
    }
}
