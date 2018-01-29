<?php

namespace app\models\search;

use app\components\Pagination;
use app\models\entities\AccountHistory;
use Yii;
use yii\data\ActiveDataProvider;

class AccountHistorySearch extends AccountHistory
{
    public function statisticsSearch($params, Pagination $pagination, int $loadTime)
    {
        $query = self::find()
            ->where(['user_id' => Yii::$app->user->getId()])
            ->andWhere(['<=', 'date', $loadTime])
            ->andWhere(['type' => self::TYPE['virtualMoney']])
            ->orderBy(['date' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,
        ]);

        return $dataProvider;
    }
}