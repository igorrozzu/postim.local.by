<?php

namespace app\controllers;

use app\components\cardsReviewsWidget\CardsReviewsWidget;
use app\components\Helper;
use app\components\MainController;
use app\components\Pagination;
use app\models\AddPost;
use app\models\City;
use app\models\Comments;
use app\models\entities\BusinessOrder;
use app\models\entities\FavoritesPost;
use app\models\entities\Gallery;
use app\models\entities\OwnerPost;
use app\models\moderation_post\PostsModeration;
use app\models\Posts;
use app\models\Reviews;
use app\models\ReviewsSearch;
use app\models\search\CommentsSearch;
use app\models\search\GallerySearch;
use app\models\UnderCategory;
use app\models\UnderCategoryFeatures;
use app\models\uploads\UploadPhotos;
use app\models\uploads\UploadPhotosByUrl;
use app\models\uploads\UploadPostPhotos;
use app\models\uploads\UploadPostPhotosTmp;
use linslin\yii2\curl\Curl;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class PostController extends MainController
{

    public function actionIndex(int $id, string $photo_id = null, int $review_id = null)
    {
        $query = Posts::find()
        ->where(['id'=>$id]);
        $with = ['info',
            'workingHours'=>function ($query) {
                $query->orderBy(['day_type'=>SORT_ASC]);
            },
            'city', 'totalView','onlyOnceCategories.category'];

        if(!Yii::$app->user->isGuest){
            $with[]='hasLike';
            $with[]='hasSendBs';
        }
        $post = $query->with($with)->one();


        $user_id = Yii::$app->user->isGuest ? null : Yii::$app->user->getId();

        if ($post &&
            (($post['status'] != 0 ? true : $post['user_id'] == $user_id) ||
                Yii::$app->user->isModerator())
        ) {

            Helper::checkValidUrl('/' . Yii::$app->request->pathInfo,
                Url::to(['post/index', 'url' => $post['url_name'], 'id' => $post['id']])
            );

            Helper::addViews($post->totalView);
            $breadcrumbParams = $this->getParamsForBreadcrumb($post);

            $queryPost = Posts::find()->where(['tbl_posts.id' => $id])
                ->prepare(Yii::$app->db->queryBuilder)
                ->createCommand()->rawSql;
            $keyForMap = Helper::saveQueryForMap($queryPost);

            $reviewsModel = new ReviewsSearch();
            $_GET['postId'] = $id;
            $reviewPageSize = Yii::$app->request->get('per-page', 5);

            if($review_id){
                $newReviewsPageSize = ReviewsSearch::getNumberReviews($id, $review_id);
                if($reviewPageSize < $newReviewsPageSize){
                    $reviewPageSize = $newReviewsPageSize;
                }
            }

            $pagination = new Pagination([
                'pageSize' => $reviewPageSize,
                'page' => Yii::$app->request->get('page', 1) - 1,
                'route' => 'post/get-reviews',
                'selfParams' => [
                    'postId' => true,
                    'type' => true
                ],
            ]);
            $type = Yii::$app->request->get('type', 'all');
            $loadTime = time();
            $dataProvider = $reviewsModel->search(
                [
                    'post_id' => $id,
                    'type' => $type
                ],
                $pagination,
                $loadTime
            );



            $commentsSearch = new CommentsSearch();

            $defaultLimit = isset($comment_id) ? 1000 : 16;
            $_GET['type_entity'] = Comments::TYPE['posts'];
            $paginationComments= new Pagination([
                'pageSize' => Yii::$app->request->get('per-page', $defaultLimit),
                'page' => Yii::$app->request->get('page', 1) - 1,
                'route' => '/comments/get-comments',
                'selfParams'=>[
                    'id'=>true,
                    'type_entity'=>true
                ]
            ]);

            $dataProviderComments = $commentsSearch->search( Yii::$app->request->queryParams,
                $paginationComments,
                $post['id'],
                CommentsSearch::getSortArray('old')
            );


            return $this->render('index', [
                'post' => $post,
                'reviewsDataProvider' => $dataProvider,
                'breadcrumbParams' => $breadcrumbParams,
                'photoCount' => Gallery::getPostPhotoCount($id),
                'previewPhoto' => Gallery::getPreviewPostPhoto($id, 4),
                'keyForMap'=>$keyForMap,
                'loadTime' => $loadTime,
                'type' => $type,
                'initPhotoSliderParams' => [
                    'photoId' => $photo_id,
                    'reviewId' => $review_id,
                ],
                'dataProviderComments' => $dataProviderComments
            ]);

        } else {
            throw new NotFoundHttpException();
        }

    }


    public function actionGetReviews(int $postId){


        $reviewsModel = new ReviewsSearch();
        $pagination = new Pagination([
            'pageSize' => Yii::$app->request->get('per-page', 1),
            'page' => Yii::$app->request->get('page', 1) - 1,
            'route' => 'post/get-reviews',
            'selfParams' => [
                'postId' => true,
                'type' => true
            ],
        ]);

        $type = Yii::$app->request->get('type', 'all');

        $loadTime = Yii::$app->request->get('loadTime', time());
        $dataProvider = $reviewsModel->search(
            ['post_id' => $postId,'type'=>$type],
            $pagination,
            $loadTime,
            true
        );

        if(Yii::$app->request->isAjax && !Yii::$app->request->get('_pjax',false) ){
            echo  CardsReviewsWidget::widget([
                'dataProvider' => $dataProvider,
                'settings'=>[
                    'show-more-btn' => true,
                    'replace-container-id' => 'feed-reviews',
                    'load-time' => $loadTime,
                    'without_header'=>true
                ]
            ]);
        }

    }

    public function actionUpdateReviews($id)
    {
        $reviewsModel = new ReviewsSearch();
        $pagination = new Pagination([
            'pageSize' => 1,
            'page' => 0,
            'selfParams' => [
                'id' => true,
            ],
        ]);

        $loadTime = time();
        $dataProvider = $reviewsModel->search(
            ['post_id' => $id],
            $pagination,
            $loadTime
        );

        return $this->renderAjax('__reviews', ['reviewsDataProvider' => $dataProvider, 'post_id' => $id]);
    }

    public function actionPostModeration($id)
    {
        $post = PostsModeration::find()->with([
            'info',
            'workingHours' => function ($query) {
                $query->orderBy(['day_type' => SORT_ASC]);
            },
        ])
            ->where(['main_id' => $id])
            ->andWhere(['user_id' => Yii::$app->user->getId()])
            ->one();

        if ($post) {
            Helper::addViews($post->totalView);

            $queryPost = PostsModeration::find()->where(['tbl_posts_moderation.id' => $id])
                ->prepare(Yii::$app->db->queryBuilder)
                ->createCommand()->rawSql;
            $keyForMap = Helper::saveQueryForMap($queryPost);

            return $this->render('index_moderation', [
                'post' => $post,
                'keyForMap' => $keyForMap
            ]);
        } else {
            throw new NotFoundHttpException();
        }
    }

    public function actionPostCompare(int $id,$main_id){

        if(!Yii::$app->user->isModerator()){
            throw new NotFoundHttpException();
        }

        $post = PostsModeration::find()->with([
            'info',
            'workingHours' => function ($query) {
                $query->orderBy(['day_type' => SORT_ASC]);
            },
        ])
            ->where(['main_id' => $main_id])
            ->andWhere(['id' => $id])
            ->one();

        $mainPost = Posts::find()->with([
            'info',
            'workingHours' => function ($query) {
                $query->orderBy(['day_type' => SORT_ASC]);
            },
        ])
            ->where(['id' => $main_id])
            ->one();


        if ($post && $mainPost) {
            Helper::addViews($post->totalView);

            $queryPost = PostsModeration::find()->where(['tbl_posts_moderation.id' => $id])
                ->prepare(Yii::$app->db->queryBuilder)
                ->createCommand()->rawSql;
            $keyForMap = Helper::saveQueryForMap($queryPost);

            return $this->render('index_compare', [
                'post' => $post,
                'mainPost'=>$mainPost,
                'keyForMap' => $keyForMap
            ]);
        } else {
            throw new NotFoundHttpException();
        }
    }

    public function getParamsForBreadcrumb($post)
    {
        $breadcrumbParams = [];

        $currentUrl = Yii::$app->getRequest()->getHostInfo();
        $breadcrumbParams[] = [
            'name' => ucfirst(Yii::$app->getRequest()->serverName),
            'url_name' => $currentUrl,
            'pjax' => 'class="main-header-pjax a"'
        ];

        if ($post->city) {
            $currentUrl = $currentUrl . $post->city['url_name'] ? '/' . $post->city['url_name'] : '';
            $breadcrumbParams[] = [
                'name' => $post->city['name'],
                'url_name' => $currentUrl,
                'pjax' => 'class="main-header-pjax a"'
            ];
        }

        if (isset($post->onlyOnceCategories[0]['category'])) {
            $breadcrumbParams[] = [
                'name' => $post->onlyOnceCategories[0]['category']['name'],
                'url_name' => $currentUrl . '/' . $post->onlyOnceCategories[0]['category']['url_name'],
                'pjax' => 'class="main-header-pjax a"'
            ];
        }

        if (isset($post->onlyOnceCategories[0])) {
            $currentUrl = $currentUrl . '/' . $post->onlyOnceCategories[0]['url_name'];
            $breadcrumbParams[] = [
                'name' => $post->onlyOnceCategories[0]['name'],
                'url_name' => $currentUrl,
                'pjax' => 'class="main-header-pjax a"'
            ];
        }

        $breadcrumbParams[] = [
            'name' => $post['data'],
            'url_name' => $post['url_name'] . '-p' . $post['id'],
            'pjax' => 'class="main-pjax a"'
        ];

        return $breadcrumbParams;


    }

    public function actionFavoriteState()
    {
        $response = new \stdClass();
        $response->status = 'OK';
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;
        if ($request->isAjax && !Yii::$app->user->isGuest) {
            $itemId = (int)$request->post('itemId');

            $post = Posts::find()->select('count_favorites,id')->with('hasLike')->where(['id' => $itemId])->one();

            if ($post->hasLike) {
                if ($post->updateCounters(['count_favorites' => -1])) {
                    if ($post->hasLike->delete()) {
                        $response->status = 'remove';
                    }
                }
            } else {
                if ($post->updateCounters(['count_favorites' => 1])) {
                    $model = new FavoritesPost([
                        'user_id' => Yii::$app->user->id,
                        'post_id' => $post->id
                    ]);
                    if ($model->save()) {
                        $response->status = 'add';
                    }
                }

            }
            $response->count = $post->count_favorites;

        }
        return $response;
    }

    public function actionUploadPhoto()
    {
        if (Yii::$app->user->isGuest) {
            throw new NotFoundHttpException('Cтраница не найдена');
        }

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $model = new UploadPostPhotos();
            $model->files = UploadedFile::getInstancesByName('post-photos');
            $model->postId = (int)Yii::$app->request->post('postId');
            if ($model->upload()) {
                return $this->asJson([
                    'success' => true,
                    'message' => 'Изменения сохранены',
                ]);
            } else {
                return $this->asJson([
                    'success' => false,
                    'message' => 'Изображение должно быть в формате JPG, GIF или PNG. Макс. размер файла: 15 МБ. Не более 10 файлов'
                ]);
            }
        }
    }

    public function actionUploadTmpPhoto()
    {
        if (Yii::$app->user->isGuest) {
            throw new NotFoundHttpException('Cтраница не найдена');
        }

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $model = new UploadPostPhotosTmp();
            $model->files = UploadedFile::getInstancesByName('photos');
            if ($model->upload()) {
                return $this->asJson(['success' => true, 'data' => $model->getSavedFiles()]);
            } else {
                return $this->asJson([
                    'success' => false,
                    'message' => 'Изображение должно быть в формате JPG, GIF или PNG. Макс. размер файла: 15 МБ. Не более 10 файлов'
                ]);
            }
        }

    }

    public function actionUploadNewPhoto(){
        if (Yii::$app->user->isGuest) {
            throw new NotFoundHttpException('Cтраница не найдена');
        }

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $model = new UploadPhotos();
            $model->files = UploadedFile::getInstancesByName('photos');
            if ($model->upload()) {
                return $this->asJson(['success' => true, 'data' => $model->getSavedFiles()]);
            } else {
                return $this->asJson([
                    'success' => false,
                    'message' => 'Изображение должно быть в формате JPG, GIF или PNG. Макс. размер файла: 15 МБ. Не более 10 файлов'
                ]);
            }
        }
    }

    public function actionUploadNewPhotoByUrl(){
        if (Yii::$app->user->isGuest) {
            throw new NotFoundHttpException('Cтраница не найдена');
        }

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $model = new UploadPhotosByUrl();
            $model->urlToImg = Yii::$app->request->post('url');
            if ($model->upload()) {
                return $this->asJson(['success' => true, 'data' => $model->getSavedFiles()]);
            } else {
                return $this->asJson([
                    'success' => false,
                    'message' => 'Изображение должно быть в формате JPG, GIF или PNG. Макс. размер файла: 15 МБ. Не более 10 файлов'
                ]);
            }
        }
    }

    public function actionGetPhotos()
    {
        if (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $searchModel = new GallerySearch();
            $perPage = (int)$request->get('per-page', 16);
            $perPage = ($perPage < 16) ? 16 : $perPage;

            $pagination = new Pagination([
                'pageSize' => $perPage,
                'page' => $request->get('page', 1) - 1,
                'selfParams' => [
                    'type' => true,
                    'postId' => true,
                    'photo_id' => true,
                ],
            ]);
            $loadTime = $request->get('loadTime', time());
            $dataProvider = $searchModel->search(
                $request->queryParams,
                $pagination,
                $loadTime
            );
            $response = new \stdClass();

            if (isset($request->queryParams['photo_id'])) {
                $count = $searchModel->getPreviewsPhotoCount($loadTime);
                $page = (int)($count / 16);
                $dataProvider->pagination->pageSize = ($page === 0) ? 16 : ($page + 1) * 16;
                $response->data = $dataProvider->getModels();
                $dataProvider->pagination->page = $page;
                $dataProvider->pagination->pageSize = 16;
                $response->url = $dataProvider->pagination->getLinks()['next'] ?? null;
                $response->sequence = $count - 1;
            } else {
                $response->data = $dataProvider->getModels();
                $response->url = $dataProvider->pagination->getLinks()['next'] ?? null;
            }
            return $this->asJson($response);
        }
    }

    public function actionLoadMorePhotos()
    {
        if (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $searchModel = new GallerySearch();
            $pagination = new Pagination([
                'pageSize' => $request->get('per-page', 16),
                'page' => $request->get('page', 1) - 1,
                'selfParams' => [
                    'type' => true,
                    'postId' => true,
                ],
            ]);
            $loadTime = $request->get('loadTime', time());
            $dataProvider = $searchModel->search(
                $request->queryParams,
                $pagination,
                $loadTime
            );
            return $this->renderPartial('photo-list', [
                'dataProvider' => $dataProvider,
                'loadTime' => $loadTime,
                'sequence' => (int)$request->get('sequence', 0)
            ]);
        }
    }

    public function actionGetReviewsEdit(int $id)
    {

        $response = new \stdClass();
        $response->success = true;

        $reviews = Reviews::find()
            ->with('gallery')
            ->where([Reviews::tableName() . '.id' => $id, 'user_id' => Yii::$app->user->getId()])
            ->one();

        if ($reviews != null) {
            $response->html = $this->renderPartial('__edit_reviews', ['reviews' => $reviews]);
        } else {
            $response->success = false;
        }

        return $this->asJson($response);
    }

    public function actionGetReviewsComments(int $id)
    {

        $commentsSearch = new CommentsSearch();

        $defaultLimit = isset($comment_id) ? 1000 : 16;
        $_GET['type_entity'] = 2;
        $paginationComments = new Pagination([
            'pageSize' => Yii::$app->request->get('per-page', $defaultLimit),
            'page' => Yii::$app->request->get('page', 1) - 1,
            'route' => '/comments/get-comments',
            'selfParams' => [
                'id' => true,
                'type_entity' => true
            ]
        ]);

        $dataProviderComments = $commentsSearch->search(Yii::$app->request->queryParams,
            $paginationComments,
            $id,
            CommentsSearch::getSortArray('old')
        );

        $is_official_user = OwnerPost::find()
            ->innerJoin(Posts::tableName(), Posts::tableName() . '.id = ' . OwnerPost::tableName() . '.post_id')
            ->innerJoin(Reviews::tableName(), Reviews::tableName() . '.post_id = ' . Posts::tableName() . '.id')
            ->where(['owner_id' => Yii::$app->user->getId(),
                Reviews::tableName() . '.id' => $id
            ])->one();


        $totalComments = Comments::find()->where(['entity_id' => $id])->count();

        return $this->renderAjax('/comments/reviews_comments', [
                'dataProviderComments' => $dataProviderComments,
                'totalComments' => $totalComments,
                'id' => $id,
                'is_official_user' => $is_official_user
            ]
        );

    }


    public function actionGallery(int $postId, string $photo_id = null)
    {
        $request = Yii::$app->request;
        $searchModel = new GallerySearch();
        $_GET['type'] = $request->get('type', 'user');
        $pagination = new Pagination([
            'pageSize' => $request->get('per-page', 16),
            'page' => $request->get('page', 1) - 1,
            'route' => Url::to(['post/load-more-photos']),
            'selfParams' => [
                'type' => true,
                'postId' => true,
                'photo_id' => true,
            ],
        ]);
        $loadTime = $request->get('loadTime', time());
        $dataProvider = $searchModel->search(
            $request->queryParams,
            $pagination,
            $loadTime
        );

        $post = Posts::find()->with([
            'info', 'workingHours',
            'city', 'totalView',
            'hasLike', 'categories.category'
        ])->where(['id' => $postId])
            ->one();

        Helper::addViews($post->totalView);

        $photoCount = Gallery::find()
            ->where(['post_id' => $postId])
            ->count();

        $breadcrumbParams = $this->getParamsForBreadcrumb($post);
        $breadcrumbParams[] = [
            'name' => 'Фотографии',
            'url_name' => $request->getUrl(),
            'pjax' => 'class="main-pjax a"'
        ];

        $queryPost = Posts::find()->where(['tbl_posts.id' => $postId])
            ->prepare(Yii::$app->db->queryBuilder)
            ->createCommand()->rawSql;
        $keyForMap = Helper::saveQueryForMap($queryPost);

        return $this->render('feed-photos.php', [
            'dataProvider' => $dataProvider,
            'ownerPhotos' => $searchModel->getAllOnwerPhotos(),
            'post' => $post,
            'breadcrumbParams' => $breadcrumbParams,
            'photoCount' => $photoCount,
            'loadTime' => $loadTime,
            'keyForMap' => $keyForMap,
            'initPhotoSliderParams' => [
                'photoId' => $photo_id
            ]
        ]);
    }

    public function actionGetPlaceForMap(string $id)
    {
        $response = new \stdClass();
        $response->status = 'error';
        Yii::$app->response->format = Response::FORMAT_JSON;

        $queryFromRepository = Yii::$app->cache->get($id);
        if ($queryFromRepository) {
            $query = unserialize($queryFromRepository);
            $response->places = Yii::$app->db->createCommand($query)->queryAll();
            foreach ($response->places as &$place) {
                if ($place['coordinates']) {
                    $latLng = explode(',', $place['coordinates']);
                    $place['lat'] = str_replace('(', '', $latLng[0]);
                    $place['lon'] = str_replace(')', '', $latLng[1]);
                    unset($place['coordinates']);
                }
            }
            $response->status = 'OK';
        }
        return $response;
    }

    public function actionGetPopupPlaceForMap(int $id)
    {
        $post = Posts::find()
            ->with('hasLike')
            ->with(['workingHours' => function ($query) {
                $query->orderBy(['day_type' => SORT_ASC]);
            }])
            ->where(['tbl_posts.id' => $id])
            ->one();
        return $this->renderAjax('__popup_place', ['post' => $post]);
    }


    public function actionAdd()
    {

        if (!Yii::$app->user->isGuest) {
            $categories = UnderCategory::find()->select('id , name')->orderBy('name')->asArray()->all();
            $cities = City::find()->select('id, name')->orderBy('name')->asArray()->all();
            $metro = Helper::getMetro();

            $params = ['categories' => $categories,
                'cities' => $cities,
                'metro' => $metro
            ];
            return $this->render('add', ['params' => $params]);
        } else {
            throw new NotFoundHttpException();
        }

    }

    public function actionEdit(int $id, $moderation = null)
    {

        if (!Yii::$app->user->isGuest) {
            $categories = UnderCategory::find()->select('id , name')->orderBy('name')->asArray()->all();
            $cities = City::find()->select('id, name')->orderBy('name')->asArray()->all();
            $features = [];
            $photos = [];
            $post = null;
            $metro = Helper::getMetro();

            if ($moderation === null) {
                $photos = Gallery::find()->where(['post_id' => $id, 'user_status' => 1])->all();
                if ($photos == null) {
                    $photos = [];
                }
                $post = Posts::find()->where(['id' => $id])->with(['city', 'info',
                    'workingHours' => function ($query) {
                        $query->orderBy(['day_type' => SORT_ASC]);
                    },
                    'postCategory',
                    'postFeatures'
                ])->one();
            } else {
                $post = PostsModeration::find()->where(['main_id' => $id, 'user_id' => Yii::$app->user->getId()])
                    ->with(['city', 'info',
                        'workingHours' => function ($query) {
                            $query->orderBy(['day_type' => SORT_ASC]);
                        },
                        'postCategory',
                        'postFeatures'
                    ])->one();
            }

            $idsCategory = [];
            foreach ($post->postCategory as $item) {
                $idsCategory[] = $item['under_category_id'];
            }

            $features = $this->getFeaturesForEdit($idsCategory, $post->postFeatures);

            $params = ['categories' => $categories,
                'cities' => $cities,
                'post' => $post,
                'photos' => $photos,
                'moderation' => $moderation,
                'features' => $features,
                'metro'=>$metro
            ];

            return $this->render('edit', ['params' => $params]);
        } else {
            throw new NotFoundHttpException();
        }
    }

    private function getFeaturesForEdit(array $idsCategory, $featurePost)
    {

        $features = UnderCategoryFeatures::find()->select('features_id')->distinct('features_id')
            ->innerJoinWith('features')
            ->where(['under_category_id' => $idsCategory])
            ->andWhere(['main_features' => null])
            ->all();

        $response = new \stdClass();
        $response->rubrics = [];
        $response->additionally = [];

        $featurePost = ArrayHelper::index($featurePost, 'features_id');

        foreach ($features as $feature) {
            if ($feature->features->type == 1) {
                $feature->features['value'] = isset($featurePost[$feature->features['id']]) ? $featurePost[$feature->features['id']]['value'] : null;
                array_push($response->additionally, $feature->features);
            } else {
                if ($feature->features->type == 2) {
                    $feature->features['value'] = isset($featurePost[$feature->features['id']]) ? $featurePost[$feature->features['id']]['value'] : null;
                    array_unshift($response->rubrics, $feature->features);
                } else {
                    $featureStd = new \stdClass();
                    foreach ($feature->features->underFeatures as &$underFeature) {
                        $underFeature['value'] = isset($featurePost[$underFeature['id']]) ? $featurePost[$underFeature['id']]['value'] : null;
                    }
                    $featureStd->underFeatures = $feature->features->underFeatures;
                    $featureStd->id = $feature->features->id;
                    $featureStd->name = $feature->features->name;
                    $featureStd->type = $feature->features->type;
                    $featureStd->filter_status = $feature->features->filter_status;
                    $featureStd->main_features = $feature->features->main_features;
                    array_push($response->rubrics, $featureStd);
                }

            }
        }

        return $response;

    }

    public function actionSavePost()
    {
        if (!Yii::$app->user->isGuest && Yii::$app->request->isPost) {
            $addPostModel = new AddPost();
            $addPostModel->setScenario(AddPost::$SCENARIO_ADD);
            if ($addPostModel->load(Yii::$app->request->post(), '') && $addPostModel->save()) {
                $message = Yii::$app->user->identity->role > 1 ? 'place' : 'moderation';
                Yii::$app->getSession()->setFlash('redirect_after_add' . Yii::$app->user->getId(), $message);
                return $this->redirect('/id' . Yii::$app->user->getId());
            }
        }
    }

    public function actionSaveEditPost()
    {
        if (!Yii::$app->user->isGuest && Yii::$app->request->isPost) {
            $addPostModel = new AddPost();

            $scenario = '';
            $message = '';
            if (Yii::$app->user->identity->role > 1) {
                $scenario = AddPost::$SCENARIO_EDIT_MODERATOR;
                $message = 'place_edit';
            } else {

                $post = Posts::find()
                    ->where(['id' => Yii::$app->request->post('id'),
                        'user_id' => Yii::$app->user->getId(),
                        'status' => 0
                    ])
                    ->one();

                if ($post) {
                    $scenario = AddPost::$SCENARIO_EDIT_USER_SELF_POST;
                    $message = 'moderation';
                } else {
                    $scenario = AddPost::$SCENARIO_EDIT_USER;
                    $message = 'moderation_edit';
                }

            }
            $addPostModel->setScenario($scenario);

            if ($addPostModel->load(Yii::$app->request->post(), '') && $addPostModel->save()) {
                if ($scenario !== AddPost::$SCENARIO_EDIT_MODERATOR) {
                    Yii::$app->getSession()->setFlash('redirect_after_add' . Yii::$app->user->getId(), $message);
                    return $this->redirect('/id' . Yii::$app->user->getId());
                } else {
                    return $this->redirect(Url::to(['post/index', 'url' => 'redirect', 'id' => Yii::$app->request->post('id')]));
                }

            }
        }
    }

    public function actionGetFeaturesByCategories()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $ids = Yii::$app->request->get('categories');

        $features = UnderCategoryFeatures::find()->select('features_id')->distinct('features_id')
            ->innerJoinWith('features')
            ->where(['under_category_id' => $ids])
            ->andWhere(['main_features' => null])
            ->all();

        $response = new \stdClass();
        $response->rubrics = [];
        $response->additionally = [];

        foreach ($features as $feature) {
            if ($feature->features->type == 1) {
                array_push($response->additionally, $feature->features);
            } else {
                if ($feature->features->type == 2) {
                    array_unshift($response->rubrics, $feature->features);
                } else {
                    $featureStd = new \stdClass();
                    $featureStd->underFeatures = $feature->features->underFeatures;
                    $featureStd->id = $feature->features->id;
                    $featureStd->name = $feature->features->name;
                    $featureStd->type = $feature->features->type;
                    $featureStd->filter_status = $feature->features->filter_status;
                    $featureStd->main_features = $feature->features->main_features;
                    array_push($response->rubrics, $featureStd);
                }

            }
        }

        return $response;

    }

    public function actionGetCodeVideo()
    {
        $query = Yii::$app->request->get('query');

        $response = new \stdClass();

        if ($query) {
            $curl = new Curl();
            $response = $curl->get($query);
        }
        return $response;
    }

    public function actionGetPhotoInfo()
    {
        $params = [
            'src' => Yii::$app->request->get('src', '')
        ];

        return $this->renderAjax('__form_photo_info', $params);
    }

    public function actionGetFormBusinessAccount(){

        if(!Yii::$app->user->isGuest){
            $businessOrder = new BusinessOrder();
            $businessOrder->user_id = Yii::$app->user->getId();
            $businessOrder->post_id = Yii::$app->request->get('post_id',null);

            return $this->renderAjax('__form_add_business_account',['businessOrder'=>$businessOrder]);
        }else{
            throw new NotFoundHttpException();
        }

    }

    public function actionSaveBusinessAccount(){

        if(Yii::$app->request->isPost){
            $businessOrder = new BusinessOrder();
            $businessOrder->status = BusinessOrder::$BIZ_ORDER;

            if($businessOrder->load(Yii::$app->request->post()) && $businessOrder->save()){
                return $this->asJson(['success'=>true,'message'=>'Спасибо за вашу заявку. Она будет рассмотренав ближайшее время, после чего наш менеджер свяжется с вами']);
            }else{

                $message = $businessOrder->getFirstError('user_id');
                if($message){
                    $newBusinessOrder = new BusinessOrder();
                    $newBusinessOrder->user_id = $businessOrder->user_id;
                    $newBusinessOrder->post_id = $businessOrder->post_id;
                    $businessOrder = $newBusinessOrder;
                }
                $html = $this->renderPartial('__form_add_business_account',['businessOrder'=>$businessOrder]);
                return $this->asJson(['success'=>false,'html'=>$html,'message'=>$message]);
            }
        }

    }

}
