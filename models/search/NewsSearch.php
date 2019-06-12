<?php

namespace app\models\search;

use app\components\Pagination;
use app\models\City;
use app\models\entities\FavoritesNews;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\News;

/**
 * NewsSearch represents the model behind the search form about `app\models\News`.
 */
class NewsSearch extends News
{
    public $favorite_id;
    public $city;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'city_id', 'total_view_id', 'count_favorites', 'date'], 'integer'],
            [
                [
                    'header',
                    'description',
                    'data',
                    'description_s',
                    'key_word_s',
                    'cover',
                    'city',
                    'favorite_id',
                ],
                'safe',
            ],
        ];
    }

    /**
     * @return mixed
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * @param $params
     * @param Pagination $pagination
     * @param array $sort
     * @param null $loadTime
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
            'pagination' => $pagination,
        ]);

        if (!$this->load($params, '') && !$this->validate()) {
            return $dataProvider;
        }

        if (!Yii::$app->user->isGuest) {
            $query->joinWith('hasLike');
        }

        if (!empty($this->city) && $this->city['name'] != 'Беларусь') {
            $city = City::find()->with('region')->where(['url_name' => $this->city['url_name']])->one();
            $query->andWhere([
                'or',
                ['tbl_region.url_name' => $city->region->url_name],
                ['tbl_city.url_name' => $city->url_name],
                ['tbl_city.name' => 'Беларусь'],
            ]);
        }

        if (isset($params['loadTime']) || isset($loadTime)) {
            $query->andWhere(['<=', 'tbl_news.date', $params['loadTime'] ?? $loadTime]);
        }

        return $dataProvider;
    }

    /**
     * @param $params
     * @param Pagination $pagination
     * @param $loadTime
     * @return ActiveDataProvider
     */
    public function searchFavorites($params, Pagination $pagination, $loadTime)
    {
        $query = News::find()
            ->joinWith('city.region')
            ->innerJoinWith('favoriteNews')
            ->andWhere(['<=', News::tableName() . '.date', $loadTime])
            ->orderBy(['date' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,
        ]);

        if (!$this->load($params, '') && !$this->validate()) {
            return $dataProvider;
        }

        if (isset($this->favorite_id)) {
            $query->andWhere([FavoritesNews::tableName() . '.user_id' => $this->favorite_id]);
        }

        return $dataProvider;
    }
}