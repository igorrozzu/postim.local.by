<?php

namespace app\models;

use app\components\Helper;
use app\models\entities\Gallery;
use dosamigos\transliterator\TransliteratorHelper;
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
    public function search($params, Pagination $pagination, Array $sort, $loadTime = null , array $self_filters =[],$loadGeolocation=[])
    {
		$query = Posts::find();
		if(!isset($params['id'])){
			$query->orderBy(['priority'=>SORT_DESC]);
		}

        // add conditions that should always apply here
        if(Yii::$app->request->get('sort',false)=='nigh' && $loadGeolocation){
           $coordinates='POINT('.$loadGeolocation["lat"].' '.$loadGeolocation["lon"].')';
           $query->select('tbl_posts.*,ST_distance_sphere(st_point("coordinates"[0],"coordinates"[1]),ST_GeomFromText(\''.$coordinates.'\')) as distance');
           $query->addSelect(['COUNT('.Gallery::tableName().'.post_id) as number']);
           $query->addOrderBy(['distance'=>SORT_ASC]);
        }else{
            $query->select(['tbl_posts.*','COUNT('.Gallery::tableName().'.post_id) as number']);
            $query->addOrderBy($sort);
        }



        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination
        ]);

        $this->queryForPlaceOnMap=Posts::find()->select('tbl_posts.id,tbl_posts.coordinates');

        if (!$this->load($params,'') && !$this->validate()) {
            return $dataProvider;
        }

        $relations = ['city.region.coutries'];
        if(isset($this->favorite) && $this->favorite === 'posts') {
            $relations[] = 'favoritePosts';
            $this->queryForPlaceOnMap->innerJoinWith('favoritePosts');
        } else if(!Yii::$app->user->isGuest) {
            $query->joinWith('hasLike');
        }
        $query->innerJoinWith($relations);
        $query->joinWith('categories.category');

        if(isset($params['loadTime']) || isset($loadTime) ){
            $query->andWhere(['<=', 'tbl_posts.date', $params['loadTime'] ?? $loadTime]);
            $this->queryForPlaceOnMap->andWhere(['<=', 'tbl_posts.date', $params['loadTime'] ?? $loadTime]);
        }
        if(isset($params['id'])){

			$query->joinWith(['info'=>function ($query) {
				$query->select('editors');
			}]);
			$query->andWhere("editors @> '[".$params['id']."]'");

        } else if(isset($this->favorite_id)) {
            $query->andWhere(['tbl_favorites_post.user_id' => $this->favorite_id]);
            $this->queryForPlaceOnMap->andWhere(['tbl_favorites_post.user_id' => $this->favorite_id]);
        }

        if(isset($params['moderation']) && $params['moderation'] === '1'){
            $query->andWhere([Posts::tableName().'.status' => 0]);
            $this->queryForPlaceOnMap->andWhere([Posts::tableName().'.status' => 0]);
        } else {
            $query->andWhere([Posts::tableName().'.status' => 1]);
            $this->queryForPlaceOnMap->andWhere([Posts::tableName().'.status' => 1]);
        }

        if(!empty($this->city)){
            $query->andWhere(['or',
                ['tbl_region.url_name'=>$this->city['url_name']],
                ['tbl_city.url_name'=>$this->city['url_name']],
                ['tbl_countries.url_name'=>$this->city['url_name']],
            ]);

            $this->queryForPlaceOnMap->innerJoinWith('city.region.coutries');
            $this->queryForPlaceOnMap->andWhere(['or',
                ['tbl_region.url_name'=>$this->city['url_name']],
                ['tbl_city.url_name'=>$this->city['url_name']],
                ['tbl_countries.url_name'=>$this->city['url_name']],
            ]);

        }


       if($this->open){
           $query->innerJoinWith(['workingHours'=>function ($query) {
               $query->andWhere(['day_type' =>date('w')==0?7:date('w')]);
           }]);
           $currentTimestamp = Yii::$app->formatter->asTimestamp(Yii::$app->formatter->asTime($loadTime + Yii::$app->user->getTimezoneInSeconds(), 'short'));
           $currentTime = idate('H', $currentTimestamp) * 3600 + idate('i', $currentTimestamp) * 60 + idate('s', $currentTimestamp);
           $query->andWhere(['or',
               ['and',
                   ['<=', 'tbl_working_hours.time_start', $currentTime],
                   ['>=', 'tbl_working_hours.time_finish', $currentTime]
               ],
               ['and',
                   ['>=', 'tbl_working_hours.time_finish', $currentTime+24*3600],
                   ['<=', 'tbl_working_hours.time_start', $currentTime+24*3600]
               ]
           ]);

            $this->filters--;


            $this->queryForPlaceOnMap->innerJoinWith(['workingHours'=>function ($query) {
                $query->andWhere(['day_type' =>date('w')==0?7:date('w')]);
            }]);
            $this->queryForPlaceOnMap->andWhere(['or',
                ['and',
                    ['<=', 'tbl_working_hours.time_start', $currentTime],
                    ['>=', 'tbl_working_hours.time_finish', $currentTime]
                ],
                ['and',
                    ['>=', 'tbl_working_hours.time_finish', $currentTime+24*3600],
                    ['<=', 'tbl_working_hours.time_start', $currentTime+24*3600]
                ]
            ]);


        }else{
           $query->with(['workingHours'=>function ($query) {
              $query->orderBy(['day_type'=>SORT_ASC]);
           }]);
       }

       if($self_filters){

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

               $query->innerJoinWith('postFeatures');
               $this->queryForPlaceOnMap->innerJoinWith('postFeatures');

               $query->andWhere($queryFiltersBool);
               $query->having('count(distinct features_id) = '.$this->filters);

               $this->queryForPlaceOnMap->andWhere($queryFiltersBool);
               $this->queryForPlaceOnMap->having('count(distinct features_id) = '.$this->filters);
           }
       }

        if(!empty($this->under_category)){
            $query->andWhere(['tbl_under_category.url_name'=> $this->under_category['url_name']]);

            $this->queryForPlaceOnMap->joinWith('categories.category');
            $this->queryForPlaceOnMap->andWhere(['tbl_under_category.url_name'=> $this->under_category['url_name']]);

        }elseif(!empty($this->category)){
            $query->andWhere(['tbl_category.url_name'=>$this->category['url_name']]);
            $this->queryForPlaceOnMap->joinWith('categories.category');
            $this->queryForPlaceOnMap->andWhere(['tbl_category.url_name'=>$this->category['url_name']]);
        }

        if(isset($params['text'])){
            $query->andWhere(['like','upper(data)','%'.mb_strtoupper($params['text']).'%',false]);
        }

        $query->leftJoin(Gallery::tableName(),Posts::tableName().'.id = '.Gallery::tableName().'.post_id');
        $query->addOrderBy(['number'=>SORT_DESC]);
        $query->addOrderBy(['data'=>SORT_ASC]);

        $query->groupBy(['tbl_posts.id']);
        $this->queryForPlaceOnMap->groupBy(['tbl_posts.id']);

        $this->key = Helper::saveQueryForMap($this->queryForPlaceOnMap
            ->prepare(Yii::$app->db->queryBuilder)
            ->createCommand()->rawSql);
        $query->with('lastPhoto');


        return $dataProvider;
    }

    public function searchSpotlight($params){

        $query = Posts::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $this->queryForPlaceOnMap=Posts::find()->select('tbl_posts.id,tbl_posts.coordinates');

        if (!$this->load($params,'') && !$this->validate()) {
            return $dataProvider;
        }

        $relations = ['city.region.coutries'];
        if(!Yii::$app->user->isGuest) {
            $query->joinWith('hasLike');
        }
        $query->innerJoinWith($relations);
        $query->joinWith('categories.category');

        $query->andWhere([Posts::tableName().'.status' => 1]);
        $this->queryForPlaceOnMap->andWhere([Posts::tableName().'.status' => 1]);

        if(!empty($this->city)){
            $query->andWhere(['or',
                ['tbl_region.url_name'=>$this->city['url_name']],
                ['tbl_city.url_name'=>$this->city['url_name']],
                ['tbl_countries.url_name'=>$this->city['url_name']],
            ]);

            $this->queryForPlaceOnMap->innerJoinWith('city.region.coutries');
            $this->queryForPlaceOnMap->andWhere(['or',
                ['tbl_region.url_name'=>$this->city['url_name']],
                ['tbl_city.url_name'=>$this->city['url_name']],
                ['tbl_countries.url_name'=>$this->city['url_name']],
            ]);

        }

        $query->select('tbl_posts.*, MAX(tbl_reviews.date) as last_review');
        $query->innerJoin(Reviews::tableName(),'tbl_posts.id = tbl_reviews.post_id');
        $query->orderBy(['last_review'=>SORT_DESC]);

        $query->groupBy(['tbl_posts.id']);
        $query->limit(4);
        $this->queryForPlaceOnMap->groupBy(['tbl_posts.id']);

        $query->with(['workingHours'=>function ($query) {
            $query->orderBy(['day_type'=>SORT_ASC]);
        }]);

        $this->key = Helper::saveQueryForMap($this->queryForPlaceOnMap
            ->prepare(Yii::$app->db->queryBuilder)
            ->createCommand()->rawSql);
        $query->with('lastPhoto');


        return $dataProvider;


    }

    public function getAutoComplete(string $text){
        $query = Posts::find()->select(['tbl_posts.id','tbl_posts.data','tbl_posts.url_name','tbl_posts.city_id'])
            ->where(['like','upper(data)','%'.mb_strtoupper($text).'%',false]);

        if($city = Yii::$app->city->getSelected_city()['url_name']){
            $query->innerJoinWith(['city.region'])
                ->andWhere(['or',
                     ['tbl_region.url_name'=>$city],
                     ['tbl_city.url_name'=>$city]
                 ]);
        }

        $query->orderBy(['priority'=>SORT_DESC]);
        $query->addOrderBy(['data'=>SORT_ASC]);
        $query->limit(5);

        $data = $query->asArray()->all();

        if(!$data){
            $transliteText = TransliteratorHelper::process( $text );
            $query->where(['like','upper(data)','%'.mb_strtoupper($transliteText).'%',false]);
            $data = $query->asArray()->all();
        }

        return $data;

    }

    public function getKeyForPlacesOnMap()
    {
        return $this->key;
    }

    public static function getSortArray($paramSort)
    {
        switch ($paramSort){
            case 'new':{return ['date'=>SORT_DESC];}break;
            default:{return ['rating'=>SORT_DESC,'count_reviews'=>SORT_DESC];}break;
        }
    }

    public function getCountByCityAndCategory(array $params)
    {
        $this->load($params, '');

        $query = Posts::find()
            ->innerJoinWith(['city.region.coutries', 'categories.category'])
            ->andWhere([Posts::tableName() . '.status' => Posts::$STATUS['confirm']]);

        if (isset($this->city)) {
            $query->andWhere(['or',
                [Region::tableName() . '.url_name' => $this->city['url_name']],
                [City::tableName() . '.url_name' => $this->city['url_name']],
                [Countries::tableName() . '.url_name' => $this->city['url_name']],
            ]);
        }

        if (isset($this->under_category)) {
            $query->andWhere([UnderCategory::tableName() . '.url_name' => $this->under_category['url_name']]);

        } else if (isset($this->category)) {
            $query->andWhere([Category::tableName() . '.url_name' => $this->category['url_name']]);
        }

        $query->groupBy([Posts::tableName() . '.id']);

        return $query->count();
    }
}
