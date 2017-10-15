<?php

namespace app\models;

use app\models\entities\NotificationUser;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;

/**
 * NotificationSearch represents the model behind the search form about `app\models\entities\NotificationUser`.
 */
class NotificationSearch extends NotificationUser
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['notification_id', 'user_id', 'is_showed'], 'integer']
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
        $query = NotificationUser::find()
            ->joinWith(['notification.sender'])
            ->where(['user_id' => Yii::$app->user->id])
            ->andWhere(['<=', 'date', $params['time']])
            ->orderBy(['date' => SORT_DESC, 'notification_id' => SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,
        ]);

        return $dataProvider;
    }
}
