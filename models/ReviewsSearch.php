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
    public function search($params, Pagination $pagination, $loadTime = null, $without_header = false)
    {
        $query = Reviews::find()
            ->joinWith(['user.userInfo'])
            ->innerJoinWith(['post'])
            ->where(['<=', 'tbl_reviews.date', $params['loadTime'] ?? $loadTime])
            ->orderBy(['tbl_reviews.date' => SORT_DESC]);

        if(!$without_header){
			$query->with(['post.categories']);
		}
		$query->with('officialAnswer');

        // add conditions that should always apply here
        if(isset($params['id'])) {
            $query->andWhere(['tbl_reviews.user_id' => $params['id']]);
        }

        if(isset($params['post_id'])){
			$query->andWhere(['tbl_posts.id' => $params['post_id']]);
		}

        if(isset($params['region']) && !empty($params['region'])) {
            $query->innerJoinWith(['post.city.region'], false)
				 ->andWhere(['or',
					 ['tbl_region.name'=>$params['region']],
					 ['tbl_city.name'=>$params['region']]
				 ]);
        }
        if(isset($params['type']) && $params['type'] !== 'all') {
            if($params['type'] === 'positive') {
                $query->andWhere(['>=', 'tbl_reviews.rating', self::BORDER_DIVIDED_REVIEWS]);
            } else if ($params['type'] === 'negative') {
                $query->andWhere(['<', 'tbl_reviews.rating', self::BORDER_DIVIDED_REVIEWS]);
            }
        }

        if(!Yii::$app->user->isGuest){
        	$query->with(['hasLike','hasComplaint']);
		}

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,
        ]);
        $query->groupBy(['tbl_reviews.id']);

        return $dataProvider;
    }
}
