<?php

namespace app\modules\admin\models;

use phpDocumentor\Reflection\Types\Null_;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\admin\models\Features;

/**
 * FeaturesSearch represents the model behind the search form about `app\modules\admin\models\Features`.
 */
class FeaturesSearch extends Features
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name', 'main_features'], 'safe'],
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
    public function search($params, $pagination)
    {
        $query = Features::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'type' => $this->type,
            'filter_status' => $this->filter_status,
        ]);

        $query->joinWith(['mainFeatures m']);

        if($this->main_features){

            $query->andFilterWhere(['like', 'm.name', $this->main_features]);
        }

        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'tbl_features.name', $this->name]);

        return $dataProvider;
    }
}