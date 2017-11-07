<?php

namespace app\modules\admin\models\post;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\admin\models\post\Posts;

/**
 * PostsSearch represents the model behind the search form about `app\modules\admin\models\post\Posts`.
 */
class PostsSearch extends Posts
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'city_id', 'count_favorites', 'count_reviews', 'date', 'status', 'user_id', 'total_view_id', 'priority', 'main_id'], 'integer'],
            [['url_name', 'cover', 'data', 'address', 'additional_address', 'coordinates', 'title', 'description', 'key_word', 'requisites', 'metro'], 'safe'],
            [['rating'], 'number'],
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
    public function search($params,$pagination)
    {
        $query = Posts::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'city_id' => $this->city_id,
            'rating' => $this->rating,
            'count_favorites' => $this->count_favorites,
            'count_reviews' => $this->count_reviews,
            'date' => $this->date,
            'status' => $this->status,
            'user_id' => $this->user_id,
            'total_view_id' => $this->total_view_id,
            'priority' => $this->priority,
            'main_id' => $this->main_id,
        ]);

        $query->andFilterWhere(['like', 'url_name', $this->url_name])
            ->andFilterWhere(['like', 'cover', $this->cover])
            ->andFilterWhere(['like', 'data', $this->data])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'additional_address', $this->additional_address])
            ->andFilterWhere(['like', 'coordinates', $this->coordinates])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'key_word', $this->key_word])
            ->andFilterWhere(['like', 'requisites', $this->requisites])
            ->andFilterWhere(['like', 'metro', $this->metro]);

        return $dataProvider;
    }
}