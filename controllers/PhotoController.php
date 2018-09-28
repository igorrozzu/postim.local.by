<?php

namespace app\controllers;

use app\components\MainController;
use app\models\entities\Gallery;
use app\models\ReviewsGallery;
use yii\db\Query;

class PhotoController extends MainController
{
    public function actionGetForReview(int $review_id)
    {
        $response = new \stdClass();
        $response->data = Gallery::find()
            ->innerJoinWith([
                'reviewsGallery' => function (Query $query) use ($review_id) {
                    $query->where([ReviewsGallery::tableName() . '.review_id' => $review_id]);
                },
                'post',
            ])
            ->orderBy(['id' => SORT_DESC])
            ->all();
        $response->url = null;

        foreach ($response->data as $photo) {
            $response->postInfo[] = [
                'title' => $photo->post->data,
                'url' => $photo->post->url_name,
            ];
        }

        return $this->asJson($response);
    }
}
