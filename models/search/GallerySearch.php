<?php

namespace app\models\search;

use app\components\Pagination;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\entities\Gallery;

/**
 * GallerySearch represents the model behind the search form about `app\models\entities\Gallery`.
 */
class GallerySearch extends Gallery
{
    public $type;
    public $postId;
    public $userId;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'post_id', 'user_id', 'user_status', 'status', 'date'], 'integer'],
            [['link', 'type', 'postId', 'userId'], 'safe'],
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
    public function search($params, Pagination $pagination, $loadTime = null): ActiveDataProvider
    {
        $query = Gallery::find()
            ->joinWith('user')
            ->where(['<=', 'date', $loadTime])
            ->orderBy(['user_status' => SORT_DESC, 'id' => SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination
        ]);

        $this->load($params, '');
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andWhere(['post_id' => $this->postId]);
        if($this->type === 'user') {
            $query->andWhere(['user_status' => Gallery::USER_STATUS['user']]);
        }

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchProfilePhotos($params, Pagination $pagination, $loadTime = null): ActiveDataProvider
    {
        $query = Gallery::find()
            ->innerJoinWith('post')
            ->where(['<=', Gallery::tableName().'.date', $loadTime])
            ->orderBy(['id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination
        ]);

        $this->load($params, '');
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andWhere([Gallery::tableName().'.user_id' => $this->userId]);
        return $dataProvider;
    }

    public function getAllOnwerPhotos(): array
    {
        return Gallery::find()
            ->where(['post_id' => $this->postId, 'user_status' => Gallery::USER_STATUS['owner']])
            ->orderBy(['id' => SORT_DESC])->all();
    }
}