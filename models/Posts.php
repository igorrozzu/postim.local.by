<?php

namespace app\models;

use app\models\entities\FavoritesPost;
use Yii;

/**
 * This is the model class for table "tbl_posts".
 *
 * @property integer $id
 * @property string $url_name
 * @property integer $city_id
 * @property string $cover
 * @property integer $rating
 * @property string $data
 * @property string $address
 * @property integer $count_favorites
 * @property integer $count_reviews
 */
class Posts extends \yii\db\ActiveRecord
{

    public $is_like=false;
    public $is_open=true;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_posts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url_name', 'city_id', 'under_category_id', 'cover', 'rating', 'data'], 'required'],
            [['url_name', 'cover', 'data', 'address'], 'string'],
            [['city_id', 'rating', 'count_favorites', 'count_reviews'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'url_name' => 'Url Name',
            'city_id' => 'City ID',
            'under_category_id' => 'Under Category ID',
            'cover' => 'Cover',
            'rating' => 'Rating',
            'data' => 'Data',
            'address' => 'Address',
            'count_favorites' => 'Count Favorites',
            'count_reviews' => 'Count Reviews',
        ];
    }

    public function getCategories(){
       return $this->hasOne(UnderCategory::className(),['id'=>'under_category_id']);
    }

    public function getCity(){
      return  $this->hasOne(City::className(),['id'=>'city_id']);
    }

    public function getReviews()
    {
        return $this->hasMany(Reviews::className(),['post_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFavoritePosts()
    {
        return $this->hasMany(FavoritesPost::className(),['post_id' => 'id']);
    }

    public function getHasLike()
    {
        return $this->hasOne(FavoritesPost::className(),['post_id' => 'id'])
            ->onCondition([FavoritesPost::tableName().'.user_id' => Yii::$app->user->id]);

    }

    public function getUnderCategory()
    {
        return $this->hasOne(UnderCategory::className(), ['id' => 'under_category_id']);
    }

    public function afterFind()
    {
        parent::afterFind();
        if ($this->isRelationPopulated('favoritePosts') ||
            ($this->isRelationPopulated('hasLike') && !empty($this->hasLike))) {
            $this->is_like = true;
        }
    }
}
