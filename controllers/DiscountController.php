<?php

namespace app\controllers;

use app\components\discountOrder\Order;
use app\components\Helper;
use app\components\MainController;
use app\components\Pagination;
use app\models\Discounts;
use app\models\entities\DiscountOrder;
use app\models\entities\FavoritesDiscount;
use app\models\entities\GalleryDiscount;
use app\models\entities\OwnerPost;
use app\models\Posts;
use app\models\search\DiscountSearch;
use app\models\uploads\UploadPhotos;
use app\models\uploads\UploadPhotosByUrl;
use app\models\uploads\UploadPostPhotosTmp;
use app\widgets\cardsDiscounts\CardsDiscounts;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class DiscountController extends MainController
{
    public function actionAdd(int $postId)
    {
        if (!Yii::$app->user->isModerator()) {
            if (!Yii::$app->user->isOwnerThisPost($postId)) {
                throw new NotFoundHttpException('Cтраница не найдена');
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
            'promocode_counter' => 1000,
        ]);
        $post = Posts::findOne($postId);

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {

            $response = new \stdClass();
            $response->success = false;

            $model->load(Yii::$app->request->post(), 'discount');
            $model->photos = Yii::$app->request->post('photos');

            if ($model->create()) {
                $message = Yii::$app->user->isModerator() ? 'Добавление скидки произведено успешно.' :
                    'Добавление скидки произведено успешно. Ваша скидка отправлена на модерацию.';
                Yii::$app->session->setFlash('success', $message);

                $response->success = true;
                $response->redirectUrl = Url::to([
                    'post/get-discounts-by-post',
                    'name' => $post->url_name,
                    'postId' => $postId,
                ]);

            } else {
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
            throw new NotFoundHttpException('Cтраница не найдена');
        }

        if (!Yii::$app->user->isModerator()) {
            if (!Yii::$app->user->isOwnerThisPost($discount->post_id)) {
                throw new NotFoundHttpException('Cтраница не найдена');
            }
        }

        if (Yii::$app->request->isPost) {

            $discount->load(Yii::$app->request->post(), 'discount');
            $discount->photos = Yii::$app->request->post('photos');

            if (!Yii::$app->user->isModerator()) {
                $discount->status = Discounts::STATUS['editing'];
            }
            if ($discount->edit()) {
                $message = Yii::$app->user->isModerator() ? 'Редактирование скидки произведено успешно.' :
                    'Редактирование скидки произведено успешно. Ваша скидка отправлена на модерацию.';
                Yii::$app->session->setFlash('success', $message);

                $redirectUrl = Url::to(['/discount/read', 'url' => $discount->url_name,
                    'discountId' => $discount->id]);

                return $this->redirect($redirectUrl);
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
            'pageSize' => $request->get('per-page', 1),
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

        $isCurrentUserOwner = Yii::$app->user->isOwnerThisPost($discount->post_id);
        if (in_array($discount->status, [
            Discounts::STATUS['moderation'],
            Discounts::STATUS['inactive'],
            Discounts::STATUS['editing'],
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

        Helper::addViews($discount->totalView);

        return $this->render('index', [
            'discount' => $discount,
            'post' => $post,
            'breadcrumbParams' => $breadcrumbParams,
            'economy' => $discount->calculateEconomy(),
            'isCurrentUserOwner' => $isCurrentUserOwner,
            'duration' => Yii::$app->formatter->asCustomDuration($discount->date_finish - time())
        ]);
    }

    public function actionReadAll()
    {
        $loadTime = Yii::$app->request->get('loadTime', time());

        $searchModel = new DiscountSearch();
        $pagination = new Pagination([
            'pageSize' => Yii::$app->request->get('per-page', 6),
            'page' => Yii::$app->request->get('page', 1) - 1,
            'route' => Yii::$app->request->getPathInfo()
        ]);

        $dataProvider = $searchModel->searchByCity(
            Yii::$app->request->queryParams,
            $pagination,
            $loadTime
        );

        if (Yii::$app->request->isAjax &&
            !Yii::$app->request->get('_pjax', false)) {
            return CardsDiscounts::widget([
                    'dataprovider' => $dataProvider,
                    'settings' => [
                        'show-more-btn' => true,
                        'replace-container-id' => 'feed-discounts',
                        'load-time' => $loadTime,
                        'show-distance' => false,
                    ]
            ]);
        } else {
            $breadcrumbParams = $this->getParamsForBreadcrumbFeedDiscounts();
            return $this->render('feed-discounts', [
                'dataProvider' => $dataProvider,
                'breadcrumbParams' => $breadcrumbParams,
                'loadTime' => $loadTime
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
            $discount = Discounts::findOne($discountId);
            $response = new \stdClass();
            $response->success = false;

            if (!isset($discount) || $discount->date_finish < time() ||
                $discount->status !== Discounts::STATUS['active']) {
                $response->message = 'Скидка не найдена';
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
                'promo_code' => isset($discount->promocode) && $discount->promocode !== '' ?
                    $discount->promocode : (string) $discount->promocode_counter,
                'pin_code' => null,
                'status_promo' => Discounts::STATUS['active'],
            ]);

            $transaction = Yii::$app->db->beginTransaction();
            if ($order->save()) {
                $discount->updateCounters([
                    'count_orders' => 1,
                    'promocode_counter' => 1,
                ]);
                $transaction->commit();
            } else {
                $transaction->rollBack();
            }

            $response->redirectUrl = Url::to(['user/get-promocodes']) . '?my_orders';
            $response->success = true;
            return $this->asJson($response);

        } else {
            throw new NotFoundHttpException('Cтраница не найдена');
        }
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