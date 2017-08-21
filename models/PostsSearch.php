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
    public $open;
    public $filters;
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
    public function search($params, Pagination $pagination, Array $sort, $loadTime = null , array $self_filters =[])
    {
        $query = Posts::find()->select('tbl_posts.*')->orderBy($sort);
        // add conditions that should always apply here
        $query->addOrderBy(['data'=>SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination
        ]);

        if (!$this->load($params,'') && !$this->validate()) {
            return $dataProvider;
        }

        $relations = ['city.region'];
        if(isset($this->favorite) && $this->favorite === 'posts') {
            $relations[] = 'favoritePosts';
        } else if(!Yii::$app->user->isGuest) {
            $query->joinWith('hasLike');
        }
        $query->innerJoinWith($relations);
        $query->joinWith('categories.category');

        if(isset($params['loadTime']) || isset($loadTime) ){
            $query->andWhere(['<=', 'tbl_posts.date', $params['loadTime'] ?? $loadTime]);
        }
        if(isset($params['id'])){
            $query->andWhere(['tbl_posts.user_id' => $params['id']]);
        } else if(isset($this->favorite_id)) {
            $query->andWhere(['tbl_favorites_post.user_id' => $this->favorite_id]);
        }

        if(isset($params['moderation']) && $params['moderation'] === '1'){
            $query->andWhere([Posts::tableName().'.status' => 0]);
        } else {
            $query->andWhere([Posts::tableName().'.status' => 1]);
        }

        if(!empty($this->city)){
            $query->andWhere(['or',
                ['tbl_region.url_name'=>$this->city['url_name']],
                ['tbl_city.url_name'=>$this->city['url_name']]
            ]);
        }


       if($this->open){
           $query->innerJoinWith(['workingHours'=>function ($query) {
               $query->andWhere(['day_type' =>date('w')==0?7:date('w')]);
           }]);
           $currentTimestamp = Yii::$app->formatter->asTimestamp(Yii::$app->formatter->asTime(time() + Yii::$app->user->getTimezoneInSeconds(), 'short'));
           $currentTime = idate('H', $currentTimestamp) * 3600 + idate('i', $currentTimestamp) * 60 + idate('s', $currentTimestamp);
           $query->andWhere(['<=', 'tbl_working_hours.time_start', $currentTime]);
           $query->andWhere(['>=', 'tbl_working_hours.time_finish', $currentTime]);
            $this->filters--;
        }else{
           $query->with(['workingHours'=>function ($query) {
              $query->orderBy(['day_type'=>SORT_ASC]);
           }]);
       }

       if($self_filters){
           $query->innerJoinWith('postFeatures');
           $queryFiltersBool=[0=>'or'];
           foreach ($params as $nameFilter => $value){
                if(isset($self_filters[$nameFilter])){
                    if($value == 'true'){
                        $queryFiltersBool[] = ['and',
                                                ['tbl_post_features.features_id'=>$nameFilter],
                                                ['tbl_post_features.value'=>1]
                                              ];
                    }else{
                        $minMax = explode(',',$value,2);
                        if(isset($minMax[1])){
                            $min = (int) $minMax[0];
                            $max = (int) $minMax[1];
                            $queryFiltersBool[] = ['and',
                                ['tbl_post_features.features_id'=>$nameFilter],
                                ['>=','tbl_post_features.value',$min],
                                ['<=','tbl_post_features.value',$max]
                            ];
                        }
                    }
                }
           }

           if(count($queryFiltersBool)>1){
               $query->andWhere($queryFiltersBool);
               $query->having('count(distinct features_id) = '.$this->filters);
           }
       }

        if(!empty($this->under_category)){
            $query->andWhere(['tbl_under_category.url_name'=> $this->under_category['url_name']]);
        }elseif(!empty($this->category)){
            $query->andWhere(['tbl_category.url_name'=>$this->category['url_name']]);
        }

        $query->groupBy(['tbl_posts.id']);
        $query->with('lastPhoto');


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
