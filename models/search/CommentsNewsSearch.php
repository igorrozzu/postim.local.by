<?php

namespace app\models\search;

use app\components\Pagination;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CommentsNews;

/**
 * CommentsNewsSearch represents the model behind the search form about `app\models\CommentsNews`.
 */
class CommentsNewsSearch extends CommentsNews
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'news_id', 'user_id', 'main_comment_id', 'like', 'date'], 'integer'],
            [['data'], 'safe'],
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
    public function search($params,Pagination $pagination,int $news_id,array $sort)
    {
        $query = CommentsNews::find()
            ->with('underComments.user.userInfo')
            ->with('user.userInfo')
            ->where([
                'news_id'=>$news_id,
                'main_comment_id'=>null
            ])
            ->orderBy($sort);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination
        ]);

        if(!Yii::$app->user->isGuest){
            $query->with('likeUser');
            $query->with('underComments.likeUser');
            $query->with('complaintUser');
            $query->with('underComments.complaintUser');
        }

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;
    }

    public static function getSortArray(string $paramSort):array {
        switch ($paramSort){
            case 'new':{return ['date'=>SORT_DESC];}break;
            case 'old':{return ['date'=>SORT_ASC];}break;
            default:{return ['date'=>SORT_DESC];}break;
        }
    }
}
