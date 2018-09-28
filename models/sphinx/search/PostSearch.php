<?php

namespace app\models\sphinx\search;

use app\models\Posts;
use app\models\sphinx\Post;
use Yii;
use yii\db\ActiveQuery;

class PostSearch
{
    public function getAutoCompleteQuery(int $limit)
    {
        $query = Post::find()
            ->with([
                'post' => function (ActiveQuery $query) {
                    $query->select(['id', 'data', 'url_name'])
                        ->asArray();
                },
            ])
            ->limit($limit)
            ->asArray();

        return $query;
    }

    public function getMainSearchQuery(int $loadTime)
    {
        $query = Post::find()
            ->with([
                'post' => function (ActiveQuery $query) {
                    $query->joinWith(['actualDiscounts', 'categories.category'])
                        ->with([
                            'workingHours' => function ($query) {
                                $query->orderBy(['day_type' => SORT_ASC]);
                            },
                            'lastPhoto',
                        ])
                        ->innerJoinWith(['city.region.coutries']);

                    if (!Yii::$app->user->isGuest) {
                        $query->joinWith('hasLike');
                    }
                },
            ])
            ->where('`date` <= ' . $loadTime);

        return $query;
    }
}