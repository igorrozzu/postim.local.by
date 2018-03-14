<?php

namespace app\models\search;

use app\components\Helper;
use app\components\Pagination;
use app\models\Category;
use app\models\City;
use app\models\Countries;
use app\models\Discounts;
use app\models\entities\FavoritesDiscount;
use app\models\Posts;
use app\models\Region;
use app\models\UnderCategory;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class DiscountSearch extends Discounts
{
    public $category;
    public $under_category;
    public $city;
    public $open;
    public $sort;
    public $favorite_id;
    public $exclude_discount_id;
    public $city_url_name;

    private $queryForPlaceOnMap;
    private $key;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category', 'under_category', 'city', 'open', 'sort', 'favorite_id',
                'exclude_discount_id', 'city_url_name'], 'safe'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param Pagination $pagination
     * @param int $loadTime
     *
     * @return ActiveDataProvider
     */
    public function searchByPost($params, Pagination $pagination, int $loadTime)
    {
        $query = Discounts::find()
            ->where(['post_id' => $params['postId']])
            ->andWhere(['<=', 'date_start', $loadTime])
            ->andWhere(['>=', 'status', Discounts::STATUS['active']])
            ->orderBy(['date_start' => SORT_DESC]);

        $newQuery = clone $query;
        $query->andWhere(['>', Discounts::tableName() . '.date_finish', $loadTime]);
        $newQuery->andWhere(['<=', Discounts::tableName() . '.date_finish', $loadTime]);

        $unionQuery = (new ActiveQuery(Discounts::className()))
            ->from(['unionQuery' => $query->union($newQuery, true)]);
        if (!Yii::$app->user->isGuest) {
            $unionQuery->joinWith(['hasLike']);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $unionQuery,
            'pagination' => $pagination,
        ]);

        return $dataProvider;
    }

    public function searchByCityAndCategory(array $params, $pagination, int $loadTime, $geolocation)
    {
        $this->load($params, '');

        $query = Discounts::find()
            ->innerJoinWith(['post.city.region.coutries', 'post.categories.category'])
            ->andWhere(['<=', 'date_start', $loadTime])
            ->andWhere(['>=', Discounts::tableName() . '.status', Discounts::STATUS['active']]);

        if ($this->sort === 'nigh' && $geolocation) {
            $coordinates = 'POINT(' . $geolocation["lat"] . ' ' . $geolocation["lon"] . ')';
            $query->select([
                Discounts::tableName() . '.*',
                'ST_distance_sphere(st_point("coordinates"[0],"coordinates"[1]),ST_GeomFromText(\'' .
                $coordinates . '\')) as distance'
            ])->distinct()
                ->orderBy(['distance' => SORT_ASC]);
        } else if ($this->sort === 'popular') {
            $query->groupBy([Discounts::tableName() . '.id'])
                ->orderBy([Discounts::tableName() . '.count_orders' => SORT_DESC]);
        } else {
            $query->groupBy([Discounts::tableName() . '.id'])
                ->orderBy([Discounts::tableName() . '.date_start' => SORT_DESC]);
        }

        $this->queryForPlaceOnMap = Posts::find()
            ->select([Posts::tableName() . '.id', Posts::tableName() . '.coordinates'])
            ->innerJoinWith(['discounts'])
            ->innerJoinWith(['city.region.coutries', 'categories.category'])
            ->andWhere(['<=', 'date_start', $loadTime])
            ->andWhere(['>=', Discounts::tableName() . '.status', Discounts::STATUS['active']]);

        if (isset($this->city)) {
            $query->andWhere(['or',
                [Region::tableName() . '.url_name' => $this->city['url_name']],
                [City::tableName() . '.url_name' => $this->city['url_name']],
                [Countries::tableName() . '.url_name' => $this->city['url_name']],
            ]);
            $this->queryForPlaceOnMap->andWhere(['or',
                [Region::tableName() . '.url_name' => $this->city['url_name']],
                [City::tableName() . '.url_name' => $this->city['url_name']],
                [Countries::tableName() . '.url_name' => $this->city['url_name']],
            ]);
        }

        if (isset($this->under_category)) {
            $query->andWhere([UnderCategory::tableName() . '.url_name' => $this->under_category['url_name']]);
            $this->queryForPlaceOnMap->andWhere([UnderCategory::tableName() . '.url_name' => $this->under_category['url_name']]);
        } else if (isset($this->category)) {
            $query->andWhere([Category::tableName() . '.url_name' => $this->category['url_name']]);
            $this->queryForPlaceOnMap->andWhere([Category::tableName() . '.url_name' => $this->category['url_name']]);
        }

        if ($this->open) {
            $query->innerJoinWith(['post.workingHours' => function ($query) {
                $query->andWhere(['day_type' => date('w') == 0 ? 7 : date('w')]);
            }]);
            $currentTimestamp = Yii::$app->formatter->asTimestamp(
                Yii::$app->formatter->asTime(
                    $loadTime + Yii::$app->user->getTimezoneInSeconds(), 'short')
            );
            $currentTime = idate('H', $currentTimestamp) * 3600 +
                idate('i', $currentTimestamp) * 60 + idate('s', $currentTimestamp);
            $query->andWhere(['or',
                ['and',
                    ['<=', 'tbl_working_hours.time_start', $currentTime],
                    ['>=', 'tbl_working_hours.time_finish', $currentTime]
                ],
                ['and',
                    ['>=', 'tbl_working_hours.time_finish', $currentTime + 24 * 3600],
                    ['<=', 'tbl_working_hours.time_start', $currentTime + 24 * 3600]
                ]
            ]);

            $this->queryForPlaceOnMap->innerJoinWith(['workingHours' => function ($query) {
                $query->andWhere(['day_type' => date('w') == 0 ? 7 : date('w')]);
            }]);
            $this->queryForPlaceOnMap->andWhere(['or',
                ['and',
                    ['<=', 'tbl_working_hours.time_start', $currentTime],
                    ['>=', 'tbl_working_hours.time_finish', $currentTime]
                ],
                ['and',
                    ['>=', 'tbl_working_hours.time_finish', $currentTime + 24 * 3600],
                    ['<=', 'tbl_working_hours.time_start', $currentTime + 24 * 3600]
                ]
            ]);
        }

        $newQuery = clone $query;
        $query->andWhere(['>', Discounts::tableName() . '.date_finish', $loadTime]);
        $newQuery->andWhere(['<=', Discounts::tableName() . '.date_finish', $loadTime]);

        $unionQuery = (new ActiveQuery(Discounts::className()))
            ->from(['unionQuery' => $query->union($newQuery, true)])
            ->innerJoinWith(['post']);
        if (!Yii::$app->user->isGuest) {
            $unionQuery->joinWith(['hasLike']);
        }

        $this->queryForPlaceOnMap->groupBy([Posts::tableName() . '.id']);
        $this->key = Helper::saveQueryForMap(
            $this->queryForPlaceOnMap
                ->prepare(Yii::$app->db->queryBuilder)
                ->createCommand()->rawSql
        );

        $dataProvider = new ActiveDataProvider([
            'query' => $unionQuery,
            'pagination' => $pagination,
        ]);

        return $dataProvider;
    }

    public function searchByCity($params, Pagination $pagination, int $loadTime, $geolocation = null)
    {
        $this->load($params, '');

        $query = Discounts::find()
            ->innerJoinWith(['post.city.region.coutries'])
            ->andWhere(['<=', 'date_start', $loadTime])
            ->andWhere(['>=', Discounts::tableName() . '.status', Discounts::STATUS['active']]);

        if ($this->sort === 'nigh' && $geolocation) {
            $coordinates = 'POINT(' . $geolocation["lat"] . ' ' . $geolocation["lon"] . ')';
            $query->select([
                Discounts::tableName() . '.*',
                'ST_distance_sphere(st_point("coordinates"[0],"coordinates"[1]),ST_GeomFromText(\'' .
                $coordinates . '\')) as distance'
            ])->distinct()
                ->orderBy(['distance' => SORT_ASC]);
        } else if ($this->sort === 'popular') {
            $query->groupBy([Discounts::tableName() . '.id'])
                ->orderBy([Discounts::tableName() . '.count_orders' => SORT_DESC]);
        } else {
            $query->groupBy([Discounts::tableName() . '.id'])
                ->orderBy([Discounts::tableName() . '.date_start' => SORT_DESC]);
        }

        if (isset($this->city)) {
            $query->andWhere(['or',
                [Region::tableName() . '.url_name' => $this->city['url_name']],
                [City::tableName() . '.url_name' => $this->city['url_name']],
                [Countries::tableName() . '.url_name' => $this->city['url_name']],
            ]);
        }

        $newQuery = clone $query;
        $query->andWhere(['>', Discounts::tableName() . '.date_finish', $loadTime]);
        $newQuery->andWhere(['<=', Discounts::tableName() . '.date_finish', $loadTime]);

        $unionQuery = (new ActiveQuery(Discounts::className()))
            ->from(['unionQuery' => $query->union($newQuery, true)]);
        if (!Yii::$app->user->isGuest) {
            $unionQuery->joinWith(['hasLike']);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $unionQuery,
            'pagination' => $pagination,
        ]);

        return $dataProvider;
    }

    public function searchByCityOnlyActive($params, Pagination $pagination, int $loadTime)
    {
        $this->load($params, '');

        $query = Discounts::find()
            ->innerJoinWith(['post.city.region.coutries'])
            ->andWhere(['<=', 'date_start', $loadTime])
            ->andWhere(['>', Discounts::tableName() . '.date_finish', $loadTime])
            ->andWhere(['>=', Discounts::tableName() . '.status', Discounts::STATUS['active']])
            ->groupBy([Discounts::tableName() . '.id'])
            ->orderBy([Discounts::tableName() . '.date_start' => SORT_DESC]);

        if (!empty($this->city_url_name)) {
            $query->andWhere(['or',
                [Region::tableName() . '.url_name' => $this->city_url_name],
                [City::tableName() . '.url_name' => $this->city_url_name],
                [Countries::tableName() . '.url_name' => $this->city_url_name],
            ]);
        }

        if (isset($this->exclude_discount_id)) {
            $query->andWhere(['!=', Discounts::tableName() . '.id', $this->exclude_discount_id]);
        }

        if (!Yii::$app->user->isGuest) {
            $query->joinWith(['hasLike']);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,
        ]);

        return $dataProvider;
    }

    public function getCountByCityAndCategory(array $params)
    {
        $this->load($params, '');

        $query = Discounts::find()
            ->innerJoinWith(['post.city.region.coutries', 'post.categories.category'])
            ->andWhere(['>=', Discounts::tableName() . '.status', Discounts::STATUS['active']]);

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

        $query->groupBy([Discounts::tableName() . '.id']);
        return $query->count();
    }

    public function searchFavorites($params, Pagination $pagination, $loadTime)
    {
        $query = Discounts::find()
            ->andWhere(['<=', 'date_start', $loadTime])
            ->andWhere(['>=', Discounts::tableName() . '.status', Discounts::STATUS['active']])
            ->orderBy(['date_start' => SORT_DESC]);

        $newQuery = clone $query;
        $query->andWhere(['>', Discounts::tableName() . '.date_finish', $loadTime]);
        $newQuery->andWhere(['<=', Discounts::tableName() . '.date_finish', $loadTime]);

        $unionQuery = (new ActiveQuery(Discounts::className()))
            ->from(['unionQuery' => $query->union($newQuery, true)]);

        $dataProvider = new ActiveDataProvider([
            'query' => $unionQuery,
            'pagination' => $pagination
        ]);

        if (!$this->load($params,'') && !$this->validate()) {
            return $dataProvider;
        }

        if (!Yii::$app->user->isGuest) {
            $unionQuery->innerJoinWith(['favoritesDiscount']);

            if (isset($this->favorite_id)) {
                $unionQuery->andWhere([FavoritesDiscount::tableName() . '.user_id' => $this->favorite_id]);
            }
        }

        return $dataProvider;
    }

    public function searchByInteresting($params, Pagination $pagination, int $loadTime, $post)
    {
        $this->load($params, '');

        $query = Discounts::find()
            ->innerJoinWith(['post.city.region.coutries', 'post.categories'])
            ->andWhere(['<=', 'date_start', $loadTime])
            ->andWhere(['>=', Discounts::tableName() . '.status', Discounts::STATUS['active']])
            ->andWhere(['>', Discounts::tableName() . '.date_finish', $loadTime])
            ->groupBy([Discounts::tableName() . '.id'])
            ->orderBy([Discounts::tableName() . '.date_start' => SORT_DESC]);

        if ($post->city['url_name']) {
            $query->andWhere(['or',
                [Region::tableName() . '.url_name' => $post->city['url_name']],
                [City::tableName() . '.url_name' => $post->city['url_name']],
                [Countries::tableName() . '.url_name' => $post->city['url_name']],
            ]);
        }

        $newQuery = clone $query;

        if (!empty($post->categories)) {
            $criteria[] = 'or';
            foreach ($post->categories as $category) {
                $criteria[][UnderCategory::tableName() . '.url_name'] = $category->url_name;
            }
            $query->andWhere($criteria);
        }

        $getIdsQuery = clone $query;
        $getIdsQuery->select([Discounts::tableName() . '.id']);

        $newQuery->andWhere(['not in', Discounts::tableName() . '.id', $getIdsQuery]);

        $unionQuery = (new ActiveQuery(Discounts::className()))
            ->from(['unionQuery' => $query->union($newQuery, true)]);

        if (!Yii::$app->user->isGuest) {
            $unionQuery->joinWith(['hasLike']);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $unionQuery,
            'pagination' => $pagination,
        ]);

        return $dataProvider;
    }

    public function readDiscount(int $discountId): ? Model
    {
        $query = Discounts::find()
            ->innerJoinWith(['totalView'])
            ->joinWith(['gallery', 'hasLike'])
            ->where([Discounts::tableName() . '.id' => $discountId])
            ->andWhere(['>=', Discounts::tableName() . '.status', Discounts::STATUS['active']]);

        return $query->one();
    }

    public function getDiscountsInModeration(Pagination $pagination)
    {
        $query = Discounts::find()
            ->innerJoinWith(['user'])
            ->andWhere(['!=', 'status', Discounts::STATUS['active']])
            ->orderBy(['date_start' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,
        ]);

        return $dataProvider;
    }

    /**
     * @return mixed
     */
    public function getQueryForPlaceOnMap()
    {
        return $this->queryForPlaceOnMap;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }
}
