<?php

namespace app\models;

use app\models\entities\FavoritesNews;
use Yii;

/**
 * This is the model class for table "tbl_news".
 *
 * @property integer $id
 * @property integer $city_id
 * @property string $header
 * @property string $description
 * @property string $date
 * @property string $data
 * @property string $description_s
 * @property string $key_word_s
 * @property integer $total_view_id
 * @property integer $count_favorites
 *
 * @property TblCommentsNews[] $tblCommentsNews
 */
class News extends \yii\db\ActiveRecord
{

    public $is_like=false;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_news';
    }



    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['city_id', 'header', 'description', 'data', 'total_view_id', 'date'], 'required'],
            [['city_id', 'total_view_id', 'count_favorites', 'date'], 'integer'],
            [['header', 'description', 'data', 'description_s', 'key_word_s'], 'string'],
            [['cover'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'city_id' => 'City ID',
            'header' => 'Header',
            'description' => 'Description',
            'data' => 'Data',
            'description_s' => 'Description S',
            'key_word_s' => 'Key Word S',
            'total_view_id' => 'Total View ID',
            'count_favorites' => 'Count Favorites',
            'date' => 'Date',
            'cover' => 'Cover',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommentsNews()
    {
        return $this->hasMany(CommentsNews::className(), ['news_id' => 'id']);
    }

    public function getTotalComments(){
        return $this->hasMany(CommentsNews::className(), ['news_id' => 'id'])->count();
    }

    public function getTotalView(){
        return $this->hasOne(TotalView::className(),['id'=>'total_view_id']);
    }

    public function getCity(){
        return $this->hasOne(City::className(),['id'=>'city_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFavoriteNews()
    {
        return $this->hasMany(FavoritesNews::className(),['news_id' => 'id']);
    }
}
