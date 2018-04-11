<?php

namespace app\controllers;

use app\components\discountOrder\Order;
use app\components\Helper;
use app\components\MainController;
use app\components\Pagination;
use app\models\Comments;
use app\models\Discounts;
use app\models\entities\BusinessOrder;
use app\models\entities\DiscountOrder;
use app\models\entities\FavoritesDiscount;
use app\models\entities\Task;
use app\models\Posts;
use app\models\search\CommentsSearch;
use app\models\search\DiscountSearch;
use app\models\TotalView;
use app\models\uploads\UploadPhotos;
use app\models\uploads\UploadPhotosByUrl;
use app\models\uploads\UploadPostPhotosTmp;
use app\repositories\TaskRepository;
use app\widgets\cardsDiscounts\CardsDiscounts;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Exception;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class DiscountController extends MainController
{
    public function actionAdd(int $postId)
    {
        if (!Yii::$app->user->isModerator()) {
            $owner = Yii::$app->user->getOwnerThisPost($postId);
            if (!$owner) {
                throw new NotFoundHttpException();
            }
        }

        $model = new Discounts([
            'date_start' => time(),
            'status' => Yii::$app->user->isModerator() ? Discounts::STATUS['active'] :
                Discounts::STATUS['moderation'],
            'post_id' => $postId,
            'user_id' => Yii::$app->user->getId(),
            'count_favorites' => 0,
            'count_orders' => 0,
        ]);
        $post = Posts::findOne($postId);

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {

            $response = new \stdClass();
            $response->success = false;

            $model->load(Yii::$app->request->post(), 'discount');
            $model->photos = Yii::$app->request->post('photos');

            $result = false;
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $totalView = new TotalView(['count' => 0]);
                if ($totalView->save()) {
                    $model->total_view_id = $totalView->id;
                    $model->encodeProperties();

                    if ($model->save() && $model->addPhotos()) {
                        $post->requisites = $model->requisites;

                        if ($post->update() !== false) {
                            $result = true;
                        }
                    }
                }

            } catch (Exception $e){}

            if ($result) {
                $transaction->commit();

                $message = 'Добавление скидки произведено успешно.';
                Yii::$app->session->setFlash('success', $message);

                $response->success = true;
                $response->redirectUrl = Url::to([
                    'post/get-discounts-by-post',
                    'name' => $post->url_name,
                    'postId' => $postId,
                ]);

            } else {
                $transaction->rollBack();
                $response->message = array_values($model->getFirstErrors())[0];
            }

            return $this->asJson($response);
        }

        return $this->render('add', [
            'model' => $model,
            'post' => $post
        ]);
    }

    public function actionEdit(int $id)
    {
        $discount = Discounts::find()
            ->innerJoinWith(['post'])
            ->joinWith(['gallery'])
            ->where([Discounts::tableName() . '.id' => $id])
            ->one();

        if (!isset($discount)) {
            throw new NotFoundHttpException();
        }

        if (!Yii::$app->user->isModerator()) {
            $owner = Yii::$app->user->getOwnerThisPost($discount->post_id);
            if (!$owner) {
                throw new NotFoundHttpException();
            }
        }

        if (Yii::$app->request->isPost) {

            $discount->load(Yii::$app->request->post(), 'discount');
            $discount->photos = Yii::$app->request->post('photos');

            if (!Yii::$app->user->isModerator()) {
                $discount->status = ($discount->status === Discounts::STATUS['inactive']) ?
                    Discounts::STATUS['editingAfterHiding'] : Discounts::STATUS['editing'];
            }

            $result = false;
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $discount->encodeProperties();
                if ($discount->update() && $discount->addPhotos() && $discount->editPhotos()) {
                    $discount->post->requisites = $discount->requisites;

                    if ($discount->post->update() !== false) {
                        $result = true;
                    }
                }
            } catch (Exception $e){}

            if ($result) {
                $transaction->commit();

                $message =  'Редактирование скидки произведено успешно.';
                Yii::$app->session->setFlash('success', $message);
                $redirectUrl = Url::to(['/discount/read', 'url' => $discount->url_name,
                    'discountId' => $discount->id]);

                return $this->redirect($redirectUrl);
            } else {
                $transaction->rollBack();
            }
        }


        return $this->render('edit', [
            'discount' => $discount,
            'errors' => array_values($discount->getFirstErrors()),
        ]);
    }

    public function actionUploadTmpPhoto()
    {
        if (Yii::$app->user->isGuest) {
            throw new NotFoundHttpException('Cтраница не найдена');
        }

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $model = new UploadPostPhotosTmp();
            $model->setDirectory("@webroot/discount_photo/tmp/");

            $model->files = UploadedFile::getInstancesByName('photos');
            if ($model->upload()) {
                return $this->asJson([
                    'success' => true,
                    'data' => $model->getSavedFiles(),
                    'folder' => 'discount_photo',
                ]);
            } else {
                return $this->asJson([
                    'success' => false,
                    'message' => 'Изображение должно быть в формате JPG, GIF или PNG. Макс. размер файла: 15 МБ. Не более 10 файлов'
                ]);
            }
        }
    }

    public function actionUploadNewPhoto(int $postId)
    {
        if (Yii::$app->user->isGuest) {
            throw new NotFoundHttpException('Cтраница не найдена');
        }

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $model = new UploadPhotos();
            $model->directory = '/discount-img/' . $postId . '/';
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

    public function actionUploadNewPhotoByUrl(int $postId)
    {
        if (Yii::$app->user->isGuest) {
            throw new NotFoundHttpException('Cтраница не найдена');
        }

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $model = new UploadPhotosByUrl();
            $model->directory = '/discount-img/' . $postId . '/';
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

    public function actionLoadMoreDiscounts(int $postId)
    {
        $request = Yii::$app->request;
        $model = new DiscountSearch();
        $pagination = new Pagination([
            'pageSize' => $request->get('per-page', 6),
            'page' => $request->get('page', 1) - 1,
        ]);
        $loadTime = $request->get('loadTime', time());

        $dataProvider = $model->searchByPost(
            $request->queryParams,
            $pagination,
            $loadTime
        );

        if ($request->isAjax && !$request->get('_pjax', false)) {
            return CardsDiscounts::widget([
                'dataprovider' => $dataProvider,
                'settings' => [
                    'show-more-btn' => true,
                    'replace-container-id' => 'feed-discount',
                    'load-time' => $loadTime,
                    'postId' => $postId,
                    'show-distance' => false,
                ]
            ]);
        }
    }

    public function actionLoadInterestingDiscounts(int $postId)
    {
        $post = Posts::find()
            ->innerJoinWith(['city','categories'])
            ->where([Posts::tableName() . '.id' => $postId])
            ->one();

        $request = Yii::$app->request;
        $model = new DiscountSearch();
        $pagination = new Pagination([
            'pageSize' => Yii::$app->request->get('per-page', 6),
            'page' => $request->get('page', 1) - 1,
        ]);
        $loadTime = $request->get('loadTime', time());

        $dataProvider = $model->searchByInteresting(
            $request->queryParams,
            $pagination,
            $loadTime,
            $post
        );

        if ($request->isAjax && !$request->get('_pjax', false)) {
            return CardsDiscounts::widget([
                'dataprovider' => $dataProvider,
                'settings' => [
                    'show-more-btn' => true,
                    'replace-container-id' => 'feed-discount',
                    'load-time' => $loadTime,
                    'postId' => $postId,
                    'show-distance' => true,
                ]
            ]);
        }
    }

    public function actionLoadInterestingDiscountsByCity()
    {
        $request = Yii::$app->request;
        $model = new DiscountSearch();
        $pagination = new Pagination([
            'pageSize' => Yii::$app->request->get('per-page', 6),
            'page' => $request->get('page', 1) - 1,
            'selfParams' => [
                'exclude_discount_id' => true,
                'city_url_name' => true,
            ]
        ]);
        $loadTime = $request->get('loadTime', time());

        $dataProvider = $model->searchByCityOnlyActive(
            $request->queryParams,
            $pagination,
            $loadTime
        );

        if ($request->isAjax && !$request->get('_pjax', false)) {
            return CardsDiscounts::widget([
                'dataprovider' => $dataProvider,
                'settings' => [
                    'show-more-btn' => true,
                    'replace-container-id' => 'feed-discount',
                    'load-time' => $loadTime,
                    'show-distance' => true,
                ]
            ]);
        }
    }

    public function actionRead(int $discountId)
    {
        $searchModel = new DiscountSearch();
        $discount = $searchModel->readDiscount($discountId);

        $post = Posts::find()
            ->innerJoinWith(['city.region.coutries',
                'discount' => function (ActiveQuery $query) use ($discountId) {
                $query->onCondition([Discounts::tableName() . '.id' => $discountId]);
            }])
            ->with(['lastPhoto'])
            ->andWhere([Posts::tableName() . '.status' => Posts::$STATUS['confirm']])
            ->one();

        if (!isset($discount) || !isset($post)) {
            throw new NotFoundHttpException('Cтраница не найдена');
        }

        $isCurrentUserOwner = Yii::$app->user->getOwnerThisPost($discount->post_id);
        if (in_array($discount->status, [
            Discounts::STATUS['inactive'],
            Discounts::STATUS['editingAfterHiding'],
        ])) {

            if (!Yii::$app->user->isModerator() && !$isCurrentUserOwner) {
                throw new NotFoundHttpException('Cтраница не найдена');
            }
        }

        $breadcrumbParams = $this->getParamsForBreadcrumb($discount);
        $breadcrumbParams[] = [
            'name' => $discount->header,
            'url_name' => Yii::$app->request->getUrl(),
            'pjax' => 'class="main-pjax a"'
        ];

        $commentsSearch = new CommentsSearch();

        $defaultLimit = isset($comment_id) ? 1000 : 16;
        $_GET['type_entity'] = Comments::TYPE['discount'];
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
            $discount['id'],
            CommentsSearch::getSortArray('old')
        );


        Helper::addViews($discount->totalView);

        $discountSearchModel = new DiscountSearch();
        $discountPagination = new Pagination([
            'pageSize' => Yii::$app->request->get('per-page', 6),
            'page' => Yii::$app->request->get('page', 1) - 1,
            'route' => Url::to(['discount/load-interesting-discounts-by-city']),
            'selfParams' => [
                'exclude_discount_id' => true,
                'city_url_name' => true,
            ]
        ]);

        $loadTime = time();
        $_GET['city_url_name'] = $post->city->url_name;
        $_GET['exclude_discount_id'] = $discountId;
        $dataProviderDiscounts = $discountSearchModel->searchByCityOnlyActive(
            Yii::$app->request->queryParams,
            $discountPagination,
            $loadTime
        );

        return $this->render('index', [
            'discount' => $discount,
            'post' => $post,
            'breadcrumbParams' => $breadcrumbParams,
            'loadTime' => $loadTime,
            'economy' => $discount->calculateEconomy(),
            'isCurrentUserOwner' => $isCurrentUserOwner,
            'duration' => Yii::$app->formatter->asCustomDuration($discount->date_finish - time()),
            'dataProviderComments' => $dataProviderComments,
            'dataProviderDiscounts' => $dataProviderDiscounts
        ]);
    }

    public function actionReadAll()
    {
        $loadTime = Yii::$app->request->get('loadTime', time());

        $searchModel = new DiscountSearch();
        $pagination = new Pagination([
            'pageSize' => Yii::$app->request->get('per-page', 6),
            'page' => Yii::$app->request->get('page', 1) - 1,
            'route' => Yii::$app->request->getPathInfo(),
            'selfParams' => [
                'sort' => true,
            ],
        ]);

        $geoLocation = Yii::$app->request->cookies->getValue('geolocation') ?
            Json::decode(Yii::$app->request->cookies->getValue('geolocation')) : null;

        $dataProvider = $searchModel->searchByCity(
            Yii::$app->request->queryParams,
            $pagination,
            $loadTime,
            $geoLocation
        );

        if (Yii::$app->request->isAjax &&
            !Yii::$app->request->get('_pjax', false)) {
            return CardsDiscounts::widget([
                    'dataprovider' => $dataProvider,
                    'settings' => [
                        'show-more-btn' => true,
                        'replace-container-id' => 'feed-discounts',
                        'load-time' => $loadTime,
                        'show-distance' => true,
                    ]
            ]);
        } else {
            $breadcrumbParams = $this->getParamsForBreadcrumbFeedDiscounts();
            return $this->render('feed-discounts', [
                'dataProvider' => $dataProvider,
                'breadcrumbParams' => $breadcrumbParams,
                'loadTime' => $loadTime,
                'sort' => Yii::$app->request->get('sort', 'new'),
            ]);
        }
    }

    public function getParamsForBreadcrumb($discount)
    {
        $post = $discount->post;
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

        $breadcrumbParams[] = [
            'name' => 'Скидки',
            'url_name' => Url::to([
                'post/get-discounts-by-post',
                'name' => $post->url_name,
                'postId' => $post->id,
            ]),
            'pjax' => 'class="main-pjax a"'
        ];

        return $breadcrumbParams;
    }

    public function getParamsForBreadcrumbFeedDiscounts()
    {
        $breadcrumbParams = [];
        $currentUrl = Yii::$app->getRequest()->getHostInfo();

        $breadcrumbParams[] = [
            'name' => ucfirst(Yii::$app->getRequest()->serverName),
            'url_name' => $currentUrl,
            'pjax' => 'class="main-header-pjax a"'
        ];

        if ($city = Yii::$app->request->get('city')) {
            $currentUrl = $currentUrl . '/' . $city['url_name'];
            $breadcrumbParams[] = [
                'name' => $city['name'],
                'url_name' => $currentUrl,
                'pjax' => 'class="main-pjax a"'
            ];
        }

        $breadcrumbParams[] = [
            'name' => 'Скидки',
            'url_name' => Yii::$app->getRequest()->getUrl(),
            'pjax' => 'class="main-pjax a"'
        ];

        return $breadcrumbParams;
    }

    public function actionOrder(int $discountId)
    {
        if (Yii::$app->user->isGuest) {
            throw new NotFoundHttpException('Cтраница не найдена');
        }

        if (Yii::$app->request->isPost) {
            $discount = Discounts::find()
                ->innerJoinWith('post')
                ->where([Discounts::tableName() . '.id' => $discountId])
                ->one();
            $response = new \stdClass();
            $response->success = false;

            if (!isset($discount) || $discount->date_finish < time() ||
                $discount->status < Discounts::STATUS['active']) {
                $response->message = 'Скидка недоступна';
                return $this->asJson($response);
            }

            if ($discount->count_orders === $discount->number_purchases) {
                $response->message = 'К сожалению, все промокоды закончились.';
                return $this->asJson($response);
            }

            $lastOrderForDay = DiscountOrder::find()
                ->where([
                    'discount_id' => $discountId,
                    'user_id' => Yii::$app->user->getId()
                ])
                ->andWhere(['>', 'date_buy', time() - 24 * 3600])
                ->one();

            if (isset($lastOrderForDay)) {
                $response->message = 'Можно брать только один промокод в сутки на одну и ту же акцию.';
                return $this->asJson($response);
            }

            $order = new DiscountOrder([
                'user_id' => Yii::$app->user->id,
                'discount_id' => $discount->id,
                'date_buy' => time(),
                'date_finish' => $discount->date_finish,
                'promo_code' => isset($discount->promocode) && $discount->promocode !== '' ?
                    $discount->promocode : (string) mt_rand(1000, 9999),
                'pin_code' => null,
                'status_promo' => DiscountOrder::STATUS['active'],
                'price' => $discount->price_with_discount ?? $discount->price ?? null,
            ]);

            $transaction = Yii::$app->db->beginTransaction();
            if ($order->save()) {
                $discount->updateCounters([
                    'count_orders' => 1,
                ]);
                $transaction->commit();
            } else {
                $transaction->rollBack();
                return $this->asJson($response);
            }

            $response->redirectUrl = Url::to(['user/get-promocodes']) . '?my_orders';
            $response->success = true;

            $user = Yii::$app->user->identity;
            TaskRepository::addMailTask('NewPromocode', [
                'order_id' => $order->id,
            ]);
            TaskRepository::addMailTask('SendMessageToEmail', [
                'htmlLayout' => 'layouts/default',
                'view' => ['html' => 'reviewAboutDiscount'],
                'params' => [
                    'userName' => $user->name,
                    'discountTitle' => $discount->header,
                    'postTitle' => $discount->post->data,
                    'postUrl' => Url::to(['post/index', 'url' => $discount->post->url_name,
                        'id' => $discount->post->id], true),
                ],
                'toEmail' => $user->email,
                'subject' => "{$user->name}, оставьте отзыв о {$discount->post->data} на Postim.by"
            ], $order->date_finish);

            return $this->asJson($response);

        } else {
            throw new NotFoundHttpException('Cтраница не найдена');
        }
    }

    public function actionPrintOrder(int $OID)
    {
        if (Yii::$app->user->isGuest) {
            throw new NotFoundHttpException();
        }

        $order = DiscountOrder::find()
            ->innerJoinWith(['discount.post.city', 'discount.post.info'])
            ->where([
                DiscountOrder::tableName() . '.id' => $OID,
                DiscountOrder::tableName() . '.user_id' => Yii::$app->user->getId(),
                DiscountOrder::tableName() . '.status_promo' => DiscountOrder::STATUS['active'],
            ])->andWhere(['>', Discounts::tableName() . '.date_finish', time()])
            ->one();

        if (!$order) {
            throw new NotFoundHttpException();
        }

        $this->layout = '../../mail/layouts/without-footer';

        return $this->render('promocode', [
            'discount' => $order->discount,
            'discountOrder' => $order,
        ]);
    }

    public function actionFavoriteState()
    {
        $response = new \stdClass();

        $request = Yii::$app->request;
        if ($request->isAjax && !Yii::$app->user->isGuest) {
            $itemId = (int)$request->post('itemId');

            $discount = Discounts::find()
                ->select(['count_favorites', 'id'])
                ->joinWith('hasLike')
                ->where(['id' => $itemId])
                ->one();

            if ($discount->hasLike) {
                if ($discount->updateCounters(['count_favorites' => -1])) {
                    if ($discount->hasLike->delete()) {
                        $response->status = 'remove';
                    }
                }
            } else {
                if ($discount->updateCounters(['count_favorites' => 1])) {
                    $model = new FavoritesDiscount([
                        'user_id' => Yii::$app->user->id,
                        'discount_id' => $discount->id
                    ]);
                    if ($model->save()) {
                        $response->status = 'add';
                    }
                }

            }
            $response->count = $discount->count_favorites;

        }

        return $this->asJson($response);
    }
}