<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Posts;
use yii\data\Pagination;
use yii\data\Sort;

/**
 * PostsSearch represents the model behind the search form about `app\models\Posts`.
 */
class PostsSearch extends Posts
{
    public $category;
    public $under_category;
    public $city;
    public $favorite;
    public $favorite_id;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'city_id', 'rating', 'count_favorites', 'count_reviews', 'under_category_id'], 'integer'],
            [['url_name', 'cover', 'data', 'address','city',
                'category','under_category','city', 'favorite', 'favorite_id'], 'safe'],
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
        $query = Posts::find()->orderBy($sort);
        // add conditions that should always apply here
        $query->addOrderBy(['data'=>SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination
        ]);

        if (!$this->load($params,'') && !$this->validate()) {
            return $dataProvider;
        }

        $relations = ['categories.category','city.region'];
        if(isset($this->favorite) && $this->favorite === 'posts') {
            $relations[] = 'favoritePosts';
        } else if(!Yii::$app->user->isGuest) {
            $query->joinWith('hasLike');
        }
        $query->innerJoinWith($relations);

        if(isset($params['loadTime']) || isset($loadTime) ){
            $query->andWhere(['<=', 'tbl_posts.date', $params['loadTime'] ?? $loadTime]);
        }
        if(isset($params['id'])){
            $query->andWhere(['tbl_posts.user_id' => $params['id']]);
        } else if(isset($this->favorite_id)) {
            $query->andWhere(['tbl_favorites_post.user_id' => $this->favorite_id]);
        }

        if(isset($params['moderation']) && $params['moderation'] === '1'){
            $query->andWhere(['status' => 0]);
        } else {
            $query->andWhere(['status' => 1]);
        }

        if(!empty($this->city)){
            $query->andWhere(['or',
                ['tbl_region.url_name'=>$this->city['url_name']],
                ['tbl_city.url_name'=>$this->city['url_name']]
            ]);
        }

        if(!empty($this->under_category)){
            $query->andWhere(['tbl_under_category.url_name'=> $this->under_category['url_name']]);
        }elseif(!empty($this->category)){
            $query->andWhere(['tbl_category.url_name'=>$this->category['url_name']]);
        }

        $query->with('workingHours');


        return $dataProvider;
    }

    public static function getSortArray($paramSort){
        switch ($paramSort){
            case 'rating':{return ['rating'=>SORT_DESC];}break;
            case 'new':{return ['date'=>SORT_DESC];}break;
            default:{return ['rating'=>SORT_DESC];}break;
        }
    }
}
