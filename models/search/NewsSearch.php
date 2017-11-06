<?php

namespace app\models\search;

use app\components\Pagination;
use app\models\City;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\News;

/**
 * NewsSearch represents the model behind the search form about `app\models\News`.
 */
class NewsSearch extends News
{
    public $favorite;
    public $favorite_id;
    public $city;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'city_id', 'total_view_id', 'count_favorites', 'date'], 'integer'],
            [['header', 'description', 'data', 'description_s', 'key_word_s',
                'cover','city', 'favorite', 'favorite_id'], 'safe'],
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
        $query = News::find()
            ->joinWith('city.region')
            ->orderBy($sort);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination
        ]);

        if (!$this->load($params,'') && !$this->validate()) {
            return $dataProvider;
        }

        if(isset($this->favorite) && $this->favorite === 'news') {
            $query->innerJoinWith('favoriteNews');
        } else if(!Yii::$app->user->isGuest) {
            $query->joinWith('hasLike');
        }

        if(!empty($this->city) && $this->city['name']!='Беларусь'){
            $city = City::find()->with('region')->where(['url_name' => $this->city['url_name']])->one();
            $query->andWhere(['or',
                ['tbl_region.url_name' => $city->region->url_name],
                ['tbl_city.url_name' => $city->url_name],
                ['tbl_city.name' => 'Беларусь']
            ]);
        }

        if(isset($params['loadTime']) || isset($loadTime) ){
            $query->andWhere(['<=', 'tbl_news.date', $params['loadTime'] ?? $loadTime]);
        }
        if(isset($this->favorite_id)) {
            $query->andWhere(['tbl_favorites_news.user_id' => $this->favorite_id]);
        }

        if(isset($params['text'])){
            $query->andWhere(['like','upper(header)','%'.mb_strtoupper($params['text']).'%',false]);
        }

        return $dataProvider;
    }

    public function getAutoComplete(string $text){
        $query = News::find()->select(['tbl_news.id','tbl_news.header','tbl_news.url_name','tbl_news.city_id'])
            ->where(['like','upper(header)','%'.mb_strtoupper($text).'%',false]);

        if($city = Yii::$app->city->getSelected_city()['url_name']){
            $query->innerJoinWith(['city.region'])
                ->andWhere(['or',
                    ['tbl_region.url_name'=>$city],
                    ['tbl_city.url_name'=>$city]
                ]);
        }

        $query->addOrderBy(['date'=>SORT_DESC]);
        $query->addOrderBy(['header'=>SORT_ASC]);
        $query->limit(5);
        return $query->asArray()->all();

    }
}