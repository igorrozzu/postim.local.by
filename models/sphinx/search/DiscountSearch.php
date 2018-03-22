<?php
namespace  app\models\sphinx\search;
use app\models\Discounts;
use app\models\sphinx\Discount;
use Yii;
use yii\db\ActiveQuery;

class DiscountSearch
{
    public function getAutoCompleteQuery(int $limit)
    {
        $query = Discount::find()
            ->with(['discount' => function(ActiveQuery $query) {
                $query->select(['id', 'header', 'url_name'])
                    ->asArray();
            }])
            ->limit($limit)
            ->asArray();

        return $query;
    }

    public function getMainSearchQuery(int $loadTime)
    {
        $query = Discount::find()
            ->with(['discount' => function(ActiveQuery $query) {

                if (!Yii::$app->user->isGuest) {
                    $query->joinWith('hasLike');
                }
            }])
            ->where('`date` <= ' . $loadTime);

        return $query;
    }
}