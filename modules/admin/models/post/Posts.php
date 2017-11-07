<?php

namespace app\modules\admin\models\post;


use app\models\User;
use Yii;

/**
 * This is the model class for table "tbl_posts".
 *
 * @property integer $id
 * @property string $url_name
 * @property string $latlon
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
            [['url_name', 'city_id', 'rating', 'data', 'total_view_id'], 'required'],
            [['url_name', 'cover', 'data', 'address'], 'string'],
            [['city_id', 'count_favorites', 'count_reviews'], 'integer'],
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



    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getButtons(){

        $beginHtml = "<div class='data-grid-container-btn'>";
        $bodyHtml = "";
        $endHtml = "</div>";

        $bodyHtml.="<a title='Удалить' href='/admin/post/delete-post?id={$this->id}&act=delete' class='btn-moderation --delete'></a>";

        return $beginHtml.$bodyHtml.$endHtml;
    }


}
