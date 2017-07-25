<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Notification;
use yii\data\Pagination;

/**
 * NotificationSearch represents the model behind the search form about `app\models\Notification`.
 */
class NotificationSearch extends Notification
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'sender_id'], 'integer'],
            [['date', 'message'], 'safe'],
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
        $query = Notification::find()
            ->with('sender')
            ->where(['user_id' => Yii::$app->user->id])
            ->andWhere(['<=', 'date', $params['time']])
            ->orderBy(['date' => SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,
        ]);

        return $dataProvider;
    }
}
