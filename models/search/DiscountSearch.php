<?php

namespace app\models\search;

use app\components\Pagination;
use app\models\Category;
use app\models\City;
use app\models\Countries;
use app\models\Discounts;
use app\models\Region;
use app\models\UnderCategory;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;


class DiscountSearch extends Discounts
{
    public $category;
    public $under_category;
    public $city;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category', 'under_category', 'city'], 'safe'],
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
        $query = self::find()
            ->where(['post_id' => $params['postId']])
            ->andWhere(['<=', 'date_start', $loadTime])
            ->andWhere(['status' => self::STATUS['active']])
            ->orderBy(['date_start' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,
        ]);

        return $dataProvider;
    }

    public function searchByCityAndCategory(array $params, $pagination, int $loadTime)
    {
        $this->load($params, '');

        $query = self::find()
            ->innerJoinWith(['post.city.region.coutries', 'post.categories.category'])
            ->andWhere(['<=', 'date_start', $loadTime])
            ->andWhere([self::tableName() . '.status' => self::STATUS['active']])
            ->orderBy(['date_start' => SORT_DESC]);

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

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,
        ]);

        return $dataProvider;
    }

    public function readDiscount(int $discountId): ? Model
    {
        $query = Discounts::find()
            ->innerJoinWith(['post', 'totalView'])
            ->joinWith(['gallery'])
            ->where([Discounts::tableName() . '.id' => $discountId]);

        return $query->one();
    }

    public function getDiscountsInModeration(Pagination $pagination)
    {
        $query = self::find()
            ->innerJoinWith(['user'])
            ->andWhere(['status' => self::STATUS['moderation']])
            ->orderBy(['date_start' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,
        ]);

        return $dataProvider;
    }
}
