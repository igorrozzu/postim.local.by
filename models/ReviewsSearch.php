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
    const BORDER_DIVIDED_REVIEWS = 3;
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
            ->joinWith(['user.userInfo'])
            ->innerJoinWith(['post.categories'])
            ->where(['<=', 'tbl_reviews.date', $params['loadTime'] ?? $loadTime])
            ->orderBy(['tbl_reviews.date' => SORT_DESC]);

        // add conditions that should always apply here
        if(isset($params['id'])) {
            $query->andWhere(['tbl_reviews.user_id' => $params['id']]);
        }

        if(isset($params['region'])) {
            $query->innerJoinWith(['post.city'], false)
                ->where(['tbl_city.name' => $params['region']]);
        }
        if(isset($params['type']) && $params['type'] !== 'all') {
            if($params['type'] === 'positive') {
                $query->andWhere(['>=', 'tbl_reviews.rating', self::BORDER_DIVIDED_REVIEWS]);
            } else if ($params['type'] === 'negative') {
                $query->andWhere(['<', 'tbl_reviews.rating', self::BORDER_DIVIDED_REVIEWS]);
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,
        ]);
        $query->groupBy(['tbl_reviews.id']);

        return $dataProvider;
    }
}
