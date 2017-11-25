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

	public $city;

    /**
     * @inheritdoc
     */
    public function rules()
    {
		return [
			[['city'], 'safe'],
		];
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

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => $pagination,
		]);

		if (!$this->load($params,'') && !$this->validate()) {
			return $dataProvider;
		}

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

		if (isset($params['review_id']) && $params['review_id'] && !isset($params['photo_id'])) {
            $query->andWhere(['tbl_reviews.id' => $params['review_id']]);
        }

        $query->andWhere(['or',
            ['<>','tbl_reviews.status',self::$STATUS['private']],
            ['=','tbl_reviews.user_id',Yii::$app->user->getId()]
        ]);

        if($params['onlyConfirm']??false){
            $query->andWhere(['=','tbl_reviews.status',self::$STATUS['confirm']]);
        }

        if(!empty($this->city)) {
            $query->innerJoinWith(['post.city.region.coutries'], false)
				 ->andWhere(['or',
					 ['tbl_region.url_name'=>$this->city['url_name']],
					 ['tbl_city.url_name'=>$this->city['url_name']],
                     ['tbl_countries.url_name'=>$this->city['url_name']],
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

        $query->groupBy(['tbl_reviews.id']);

        return $dataProvider;
    }
}
