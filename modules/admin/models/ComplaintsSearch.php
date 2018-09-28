<?php

namespace app\modules\admin\models;

use app\components\Pagination;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\admin\models\Complaints;

/**
 * ComplaintsSearch represents the model behind the search form about `app\modules\admin\models\Complaints`.
 */
class ComplaintsSearch extends Complaints
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'entities_id', 'date'], 'integer'],
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
    public function search($params, Pagination $pagination)
    {
        $query = Complaints::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->addOrderBy(['status' => SORT_ASC, 'date' => SORT_DESC,]);
        $query->with(['user']);

        // grid filtering conditions
        $query->andFilterWhere([
            'status' => $this->status,
        ]);


        return $dataProvider;
    }
}