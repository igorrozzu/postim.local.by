<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Notification;
use yii\data\Pagination;

/**
 * NotificationSearch represents the model behind the search form about `app\models\Notification`.
 */
class ReviewsSearch extends Reviews
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), []);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, Pagination $pagination, $loadTime = null)
    {
        $query = Reviews::find()
            ->joinWith(['user', 'userInfo'])
            ->where(['tbl_reviews.user_id' => $params['id']])
            ->andWhere(['<=', 'tbl_reviews.date', $params['loadTime'] ?? $loadTime])
            ->orderBy(['tbl_reviews.date' => SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,
        ]);

        return $dataProvider;
    }
}
