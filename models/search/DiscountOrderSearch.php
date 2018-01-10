<?php

namespace app\models\search;

use app\components\Pagination;
use app\models\Discounts;
use app\models\entities\DiscountOrder;
use app\models\entities\OwnerPost;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * DiscountOrderSearch represents the model behind the search form about `app\models\entities\DiscountOrder`.
 */
class DiscountOrderSearch extends DiscountOrder
{
    public $status;
    public $type;
    public $order_time;
    public $search_field;

    private $timeRange;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'discount_id', 'date_buy', 'pin_code', 'status_promo'], 'integer'],
            [['promo_code', 'status', 'type', 'order_time', 'search_field'], 'safe'],
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
    public function search($params, Pagination $pagination, $loadTime = null)
    {
        $table = DiscountOrder::tableName();
        $query = DiscountOrder::find()
            ->innerJoinWith(['discount'])
            ->where([$table . '.user_id' => \Yii::$app->user->id])
            ->andWhere(['<=', $table . '.date_buy', $params['loadTime'] ?? $loadTime])
            ->orderBy([$table . '.date_buy' => SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,
        ]);

        $this->load($params, '');
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if($this->status !== 'all') {
            if ($this->status === 'active') {
                $query->andWhere([$table . '.status_promo' => 1]);
            } else if ($this->status === 'unactive') {
                $query->andWhere([$table . '.status_promo' => 0]);
            }
        }

        if ($this->type === 'promocode') {
            $query->andWhere([$table . '.pin_code' => null]);
        } else if ($this->type === 'certificate') {
            $query->andWhere(['not', [$table . '.pin_code' => null]]);
        }


        return $dataProvider;
    }

    public function statisticsSearch($params, Pagination $pagination, $loadTime = null)
    {
        $table = DiscountOrder::tableName();
        $query = DiscountOrder::find()
            ->innerJoinWith(['discount.ownerPost'])
            ->onCondition([OwnerPost::tableName() . '.owner_id' => \Yii::$app->user->id])
            ->andWhere(['<=', $table . '.date_buy', $params['loadTime'] ?? $loadTime])
            ->orderBy([$table . '.date_buy' => SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,
        ]);

        $this->load($params, '');
        if (!$this->validate()) {
            return $dataProvider;
        }

        if($this->status !== 'all') {
            if ($this->status === 'active') {
                $query->andWhere([$table . '.status_promo' => DiscountOrder::STATUS['active']]);
            } else if ($this->status === 'unactive') {
                $query->andWhere([$table . '.status_promo' => DiscountOrder::STATUS['inactive']]);
            }
        }

        if ($this->type === 'promocode') {
            $query->andWhere([Discounts::tableName() . '.type' => DiscountOrder::TYPE['promoCode']]);
        } else if ($this->type === 'certificate') {
            $query->andWhere([Discounts::tableName() . '.type' => DiscountOrder::TYPE['certificate']]);
        }

        if(isset($this->order_time)) {

            $time = mktime(0, 0, 0);
            $current_month = (int)date('n');
            $time_current_month = mktime(0,0,0, $current_month, 1);

            if ($current_month === 1) {
                $time_prev_month = mktime(0,0,0, 12, 1, (int) date('Y') - 1);
            } else {
                $time_prev_month = mktime(0,0,0, $current_month - 1, 1);
            }

            $timeZone = Yii::$app->user->getTimezoneInSeconds();
            $timeForView = strtotime(date('d.m.Y', time() + $timeZone));

            switch ($this->order_time) {
                case 'today': $query->andWhere(['>=', $table . '.date_buy', $time]);
                    $this->timeRange = date('d.m.Y', $timeForView);
                    break;
                case 'yesterday': $query->andWhere(['>=', $table . '.date_buy', $time - 3600 * 12])
                                        ->andWhere(['<', $table . '.date_buy', $time]);
                    $this->timeRange = date('d.m.Y - ', $timeForView - 3600 * 12) .
                                       date('d.m.Y', $timeForView);
                    break;
                case 'current-month': $query->andWhere(['>=', $table . '.date_buy', $time_current_month]);
                    $this->timeRange = date('d.m.Y - ', $time_current_month) .
                                       date('d.m.Y', $timeForView);
                    break;
                case 'prev-month': $query->andWhere(['>=', $table . '.date_buy', $time_prev_month])
                                         ->andWhere(['<', $table . '.date_buy', $time_current_month]);
                    $this->timeRange = date('d.m.Y - ', $time_prev_month) .
                                       date('d.m.Y', $time_current_month - 3600 * 12);
                    break;
            }
        }

        if(isset($this->search_field)) {
            $query->andWhere(['or',
                ['like', Discounts::tableName() . '.header', $this->search_field],
                ['like', $table . '.promo_code', $this->search_field],
            ]);
        }


        return $dataProvider;
    }

    /**
     * @return mixed
     */
    public function getTimeRange()
    {
        return $this->timeRange;
    }


}