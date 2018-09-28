<?php

namespace app\behaviors;

use app\models\PostCategoryCount;
use app\models\Posts;
use yii\db\ActiveRecord;

class CountCategoryPlace extends \yii\base\Behavior
{

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'countCategorySave',
            ActiveRecord::EVENT_AFTER_DELETE => 'countCategorySave',
        ];
    }

    public function countCategorySave()
    {
        $post_id = $this->owner->post_id;

        $post = Posts::find()
            ->with('categories.category')
            ->with('city.region.coutries')
            ->where(['id' => $post_id])->one();

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
                'count' => 1,
            ]);
            $model->save();
        }
    }


}