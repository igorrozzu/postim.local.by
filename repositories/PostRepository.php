<?php

namespace app\repositories;

use app\models\City;
use app\models\Countries;
use app\models\Posts;
use app\models\Region;
use app\models\UnderCategory;
use yii\base\Model;

class PostRepository extends Posts
{
    public function getRecommendedPostsIds(Model $post)
    {
        $query = self::find()
            ->select([
                self::tableName() . '.id',
            ])
            ->innerJoinWith(['city.region.coutries', 'categories.category', 'businessOwner'], false)
            ->andWhere([self::tableName() . '.status' => self::$STATUS['confirm']])
            ->groupBy([self::tableName() . '.id']);

        if ($post->city['url_name']) {
            $query->andWhere([
                'or',
                [Region::tableName() . '.url_name' => $post->city['url_name']],
                [City::tableName() . '.url_name' => $post->city['url_name']],
                [Countries::tableName() . '.url_name' => $post->city['url_name']],
            ]);
        }

        if (!empty($post->categories)) {
            $criteria[] = 'or';
            foreach ($post->categories as $category) {
                $criteria[][UnderCategory::tableName() . '.url_name'] = $category->url_name;
            }
            $query->andWhere($criteria);
        }

        return $query->column();
    }
}