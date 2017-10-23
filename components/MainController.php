<?php

namespace app\components;

use app\models\News;
use app\models\ReviewsSearch;
use app\models\search\NewsSearch;
use app\models\Posts;
use app\models\PostsSearch;
use Yii;
use app\components\Pagination;
use yii\web\Controller;
use app\models\LoginForm;

class MainController extends Controller
{
    public function init()
    {
        if (!yii::$app->user->isGuest) {
            $this->layout = 'mainAuth';
        }
    }

    public function getParamsForMainPage()
    {
        $request = Yii::$app->request;
        $city_name = $request->get('city', ['name' => false])['name'];

        if (!$city_name) {
            Yii::$app->city->setDefault();
        }

        $searchModel = new PostsSearch();
        $pagination = new Pagination([
            'pageSize' => $request->get('per-page', 4),
            'page' => $request->get('page', 1) - 1,
        ]);
        $sort = [
            'rating' => SORT_DESC,
            'count_reviews' => SORT_DESC
        ];

        $dataProvider = $searchModel->search(
            $request->queryParams, $pagination, $sort, time()
        );

        $NewsSearchModel = new NewsSearch();
        $pagination = new Pagination([
            'pageSize' => $request->get('per-page', 4),
            'page' => $request->get('page', 1) - 1,
        ]);

        $newsDataProvider = $NewsSearchModel->search(
            Yii::$app->request->queryParams,
            $pagination,
            PostsSearch::getSortArray('new'),
            time()
        );

        $reviewsModel = new ReviewsSearch();
        $pagination = new Pagination([
            'pageSize' => $request->get('per-page', 1),
            'page' => $request->get('page', 1) - 1,
        ]);

        $reviewsDataProvider = $reviewsModel->search($request->queryParams,
            $pagination,
            time()
        );

        return [
            'spotlight' => $dataProvider,
            'news' => $newsDataProvider,
            'reviews' => $reviewsDataProvider,
            'keyForMap' => $searchModel->getKeyForPlacesOnMap(),
            'initPhotoSliderParams' => [
                'photoId' => isset($request->queryParams['photo_id']) ?
                    (int) $request->queryParams['photo_id'] : null,
                'reviewId' => (int) $request->get('review_id'),
            ]
        ];
    }
}
