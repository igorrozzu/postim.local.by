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
        $query = PostsModeration::find()->orderBy(['priority'=>SORT_DESC]);
		$query->addOrderBy($sort);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination
        ]);


        if (!$this->load($params,'') && !$this->validate()) {
            return $dataProvider;
        }

        $relations = ['city.region'];

        $query->innerJoinWith($relations);
        $query->joinWith('categories.category');
        $query->With('workingHours');

        if(isset($params['loadTime']) || isset($loadTime) ){
            $query->andWhere(['<=', 'tbl_posts_moderation.date', $params['loadTime'] ?? $loadTime]);
        }

		$query->andWhere([PostsModeration::tableName().'.status' => 0]);
        $query->groupBy(['tbl_posts_moderation.id']);

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
