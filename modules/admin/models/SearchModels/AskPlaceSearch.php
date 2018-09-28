<?php

namespace app\modules\admin\models\SearchModels;

use app\components\Pagination;
use app\models\Comments;
use app\models\Posts;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;


/**
 * ComplaintsSearch represents the model behind the search form about `app\modules\admin\models\Complaints`.
 */
class AskPlaceSearch extends Comments
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

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
    public function search($params, Pagination $pagination): ActiveDataProvider
    {
        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->select('tbl_comments.*, COUNT(under_comments.id) as number_under_comments');
        $query->with(['user', 'post', 'underComments']);
        $query->leftJoin('tbl_comments AS under_comments', 'under_comments.main_comment_id = tbl_comments.id');
        $query->groupBy('tbl_comments.id');
        $query->addOrderBy(['number_under_comments' => SORT_ASC, 'date' => SORT_DESC]);
        $query->where(['tbl_comments.type_entity' => Comments::TYPE['posts'], 'tbl_comments.main_comment_id' => null]);

        return $dataProvider;
    }

    public function getHrefToPost()
    {

        if ($this->post) {
            return $this->post->getUrl();
        }

    }

    public function getPost()
    {
        return $this->hasOne(Posts::className(), ['id' => 'entity_id']);
    }

    public function hasAnswer()
    {

        if ($this->underComments) {
            return true;
        }

        return false;

    }

}