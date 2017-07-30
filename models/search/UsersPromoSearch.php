<?php

namespace app\models\search;

use app\components\Pagination;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\UsersPromo;

/**
 * UsersPromoSearch represents the model behind the search form about `app\models\UsersPromo`.
 */
class UsersPromoSearch extends UsersPromo
{
    public $status;
    public $type;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'discount_id', 'date_buy', 'date_finish', 'pin_code', 'status_promo'], 'integer'],
            [['promo_code', 'status', 'type'], 'safe'],
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
        $query = UsersPromo::find()
            ->joinWith(['discount'])
            ->where(['tbl_users_promo.user_id' => \Yii::$app->user->id])
            ->andWhere(['<=', 'tbl_users_promo.date_buy', $params['loadTime'] ?? $loadTime])
            ->orderBy(['tbl_users_promo.date_buy' => SORT_DESC]);

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
                $query->andWhere(['tbl_users_promo.status_promo' => 1]);
            } else if ($this->status === 'unactive') {
                $query->andWhere(['tbl_users_promo.status_promo' => 0]);
            }
        }

        if ($this->type === 'promocode') {
            $query->andWhere(['tbl_users_promo.pin_code' => null]);
        } else if ($this->type === 'certificate') {
            $query->andWhere(['not', ['tbl_users_promo.pin_code' => null]]);
        }


        return $dataProvider;
    }
}
