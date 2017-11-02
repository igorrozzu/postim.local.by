<?php

namespace app\modules\admin\models;

use app\models\ReviewsGallery;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\admin\models\Gallery;


class GallerySearch extends Gallery
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'post_id', 'user_id', 'user_status', 'status', 'date'], 'integer'],
            [['link', 'source'], 'safe'],
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
        $query = Gallery::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination'=>$pagination
        ]);

        $this->load($params);
        $query->orderBy(['status'=>SORT_ASC]);
        $query->with('post');
        $query->leftJoin(ReviewsGallery::tableName(),ReviewsGallery::tableName().'.gallery_id = '.Gallery::tableName().'.id');
        $query->where(['review_id'=>null]);

        if (!$this->validate()) {
            return $dataProvider;
        }




        return $dataProvider;
    }
}