<?php

namespace app\modules\admin\models\post;


use app\models\City;
use app\models\PostCategoryCount;
use app\models\PostUnderCategory;
use app\models\UnderCategory;
use app\models\User;
use app\modules\admin\models\UnderCategorySearch;
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

    private $post = null;

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

    public function getButtons()
    {

        $beginHtml = "<div class='data-grid-container-btn'>";
        $bodyHtml = "";
        $endHtml = "</div>";

        $bodyHtml .= "<a title='Удалить' href='/admin/post/delete-post?id={$this->id}&act=delete' class='btn-moderation --delete'></a>";

        return $beginHtml . $bodyHtml . $endHtml;
    }

    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    public function getPostUnderCategory()
    {
        return $this->hasMany(PostUnderCategory::className(), ['post_id' => 'id']);
    }

    public function getCategories()
    {
        return $this->hasMany(UnderCategory::className(), ['id' => 'under_category_id'])
            ->via('postUnderCategory');
    }

    public function beforeDelete()
    {
        $city = $this->city;
        $category = $this->categories;
        $this->post = $this;
        return parent::beforeDelete();
    }

    public function afterDelete()
    {
        parent::afterDelete();

        $this->countCategorySave();
    }

    private function countCategorySave()
    {

        $post = $this->post;

        if ($post->status == 1) {
            foreach ($post->categories as $under_category) {

                $this->savePostCategoryCount($under_category->url_name,
                    $post->city->name,
                    $post->city->url_name
                );
                $this->savePostCategoryCount($under_category->url_name,
                    $post->city->region->name,
                    $post->city->region->url_name
                );
                $this->savePostCategoryCount($under_category->url_name,
                    $post->city->region->coutries->name,
                    $post->city->region->coutries->url_name
                );

                $this->savePostCategoryCount($under_category->category->url_name,
                    $post->city->name,
                    $post->city->url_name
                );

                $this->savePostCategoryCount($under_category->category->url_name,
                    $post->city->region->name,
                    $post->city->region->url_name
                );

                $this->savePostCategoryCount($under_category->category->url_name,
                    $post->city->region->coutries->name,
                    $post->city->region->coutries->url_name
                );
            }
        }

    }

    private function savePostCategoryCount($category_name, $city_name, $city_url_name)
    {

        $model = PostCategoryCount::find()
            ->where(['category_url_name' => $category_name])
            ->andWhere(['city_name' => $city_name])->one();
        if ($model != null) {

            $query = Posts::find()
                ->joinWith('categories.category')
                ->joinWith('city.region')
                ->where(['status' => 1]);
            if (\Yii::$app->category->getCategoryByName($category_name)) {
                $query->andWhere(['tbl_category.url_name' => $category_name]);
            } else {
                $query->andWhere(['tbl_under_category.url_name' => $category_name]);
            }
            if ($city_name != 'Беларусь') {
                $query->andWhere([
                    'or',
                    ['tbl_region.url_name' => $city_url_name],
                    ['tbl_city.url_name' => $city_url_name],
                ]);
            }
            $query->groupBy('tbl_posts.id');
            $count = $query->count();
            $model->count = $count;
            $model->update();
        } else {
            $model = new PostCategoryCount([
                'category_url_name' => $category_name,
                'city_name' => $city_name,
                'count' => -1,
            ]);
            $model->save();
        }
    }


}
