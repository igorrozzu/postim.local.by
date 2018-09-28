<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\admin\models\CategoryFeatures;

/**
 * CategoryFeaturesSearch represents the model behind the search form about `app\modules\admin\models\CategoryFeatures`.
 */
class CategoryFeaturesSearch extends CategoryFeatures
{


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['features_id', 'category_id'], 'safe'],
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
        $query = CategoryFeatures::find();
        $query->joinWith(['features', 'category']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }


        $query->andFilterWhere(['like', 'tbl_features.name', $this->features_id]);
        $query->andFilterWhere(['like', 'tbl_category.name', $this->category_id]);

        return $dataProvider;
    }
}