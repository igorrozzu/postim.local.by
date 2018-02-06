<?php

namespace app\modules\admin\models\SearchModels;

use app\models\entities\BidBusinessOrder;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;


class BusinessBidSearch extends BidBusinessOrder
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
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
    public function search($params)
    {
        $query = BidBusinessOrder::find();


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 8,
            ],
        ]);
        $query->orderBy(['date'=>SORT_DESC]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;
    }

}