<?php
namespace  app\models\sphinx\search;
use app\models\sphinx\News;
use Yii;
use yii\db\ActiveQuery;

class NewsSearch
{
    public function getAutoCompleteQuery(int $limit)
    {
        $query = News::find()
            ->with(['news' => function(ActiveQuery $query) {
                $query->select(['id', 'header', 'url_name'])
                    ->asArray();
            }])
            ->limit($limit)
            ->asArray();

        return $query;
    }

    public function getMainSearchQuery(int $loadTime)
    {
        $query = News::find()
            ->with(['news' => function(ActiveQuery $query) {
                $query->joinWith('city.region');

                if (!Yii::$app->user->isGuest) {
                    $query->joinWith('hasLike');
                }
            }])
            ->where('`date` <= ' . $loadTime);

        return $query;
    }
}