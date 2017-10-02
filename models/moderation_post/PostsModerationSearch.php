<?php

namespace app\models\moderation_post;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\moderation_post\PostsModeration;
use yii\data\Pagination;


class PostsModerationSearch extends PostsModeration
{
    public $category;
    public $under_category;
    public $city;
    public $favorite;
    public $favorite_id;
    public $open;
    public $filters;

    private $queryForPlaceOnMap=null;
    private $key=null;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'city_id', 'rating', 'count_favorites', 'count_reviews'], 'integer'],
            [['url_name', 'cover', 'data', 'address','city',
                'category','under_category','city', 'favorite', 'favorite_id','open','filters'], 'safe'],
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
    public function search($params, Pagination $pagination, Array $sort, $loadTime = null)
    {

		$query1 = (new \yii\db\Query())
			->select("tbl_posts.id, tbl_posts.city_id, tbl_posts.url_name, tbl_posts.cover, tbl_posts.rating, tbl_posts.data, tbl_posts.address, tbl_posts.date, tbl_posts.main_id")
			->from('tbl_posts')
			->innerJoin('tbl_city','tbl_city.id = tbl_posts.city_id')
			->innerJoin('tbl_region','tbl_region.id = tbl_city.region_id')
			->where(['=', 'status', 0])
			->andwhere(['=', 'user_id', \Yii::$app->user->getId()])
			->andWhere(['<=', 'tbl_posts.date', $params['loadTime'] ?? $loadTime]);

		$query2 = (new \yii\db\Query())
			->select("tbl_posts_moderation.id, tbl_posts_moderation.city_id, tbl_posts_moderation.url_name, tbl_posts_moderation.cover, tbl_posts_moderation.rating, tbl_posts_moderation.data, tbl_posts_moderation.address, tbl_posts_moderation.date, tbl_posts_moderation.main_id")
			->from('tbl_posts_moderation')
			->innerJoin('tbl_city','tbl_city.id = tbl_posts_moderation.city_id')
			->innerJoin('tbl_region','tbl_region.id = tbl_city.region_id')
			->where(['=', 'status', 0])
			->andwhere(['=', 'user_id', \Yii::$app->user->getId()])
			->andWhere(['<=', 'tbl_posts_moderation.date', $params['loadTime'] ?? $loadTime]);

		$unionQuery = (new \yii\db\Query())
			->from(['dummy_name' => $query1->union($query2)])
			->orderBy(['date' => SORT_DESC]);


        $dataProvider = new ActiveDataProvider([
            'query' => $unionQuery,
            'pagination' => $pagination
        ]);

        return $dataProvider;
    }

    public function getKeyForPlacesOnMap(){
        return $this->key;
    }

    public static function getSortArray($paramSort){
        switch ($paramSort){
            case 'rating':{return ['rating'=>SORT_DESC];}break;
            case 'new':{return ['date'=>SORT_DESC];}break;
            default:{return ['rating'=>SORT_DESC];}break;
        }
    }
}
