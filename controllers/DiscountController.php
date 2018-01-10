<?php

namespace app\controllers;

use app\components\discountOrder\Order;
use app\components\Helper;
use app\components\MainController;
use app\components\Pagination;
use app\models\Discounts;
use app\models\entities\DiscountOrder;
use app\models\entities\FavoritesDiscount;
use app\models\entities\OwnerPost;
use app\models\Posts;
use app\models\search\DiscountSearch;
use app\models\uploads\UploadPhotos;
use app\models\uploads\UploadPhotosByUrl;
use app\models\uploads\UploadPostPhotosTmp;
use app\widgets\cardsDiscounts\CardsDiscounts;
use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class DiscountController extends MainController
{
    public function actionAdd(int $postId)
    {
        $currentUserId = Yii::$app->user->getId();

        if (!Yii::$app->user->isModerator()) {
            $isCurrentUserOwner = OwnerPost::find()
                ->where([
                    OwnerPost::tableName() . '.owner_id' => $currentUserId,
                    OwnerPost::tableName() . '.post_id' => $postId,
                ])->one();

            if (!isset($isCurrentUserOwner)) {
                throw new NotFoundHttpException('Cтраница не найдена');
            }
        }

        $model = new Discounts([
            'date_start' => time(),
            'status' => Yii::$app->user->isModerator() ? Discounts::STATUS['active'] :
                Discounts::STATUS['moderation'],
            'post_id' => $postId,
            'user_id' => $currentUserId,
            'count_favorites' => 0,
            'count_orders' => 0,
        ]);

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {

            $response = new \stdClass();
            $response->success = false;

            $model->load(Yii::$app->request->post(), 'discount');
            $model->photos = Yii::$app->request->post('photos');

            if ($model->create()) {
                $post = Posts::findOne($postId);

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

        if ($request->isAjax && !$request->get('_pjax',false) ){
            return CardsDiscounts::widget([
                'dataProvider' => $dataProvider,
                'settings'=>[
                    'show-more-btn' => true,
                    'replace-container-id' => 'feed-discount',
                    'loadTime' => $loadTime,
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

        if (!isset($discount)) {
            throw new NotFoundHttpException('Cтраница не найдена');
        }
        if ($discount->status === Discounts::STATUS['moderation'] ||
            $discount->status === Discounts::STATUS['inactive']) {

            if (!Yii::$app->user->isModerator() || Yii::$app->user->getId() !== $discount->user_id) {
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
            'post' => $discount->post,
            'breadcrumbParams' => $breadcrumbParams,
            'economy' => $discount->calculateEconomy(),
            'duration' => Yii::$app->formatter->asCustomDuration($discount->date_finish - time())
        ]);
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

    public function actionOrder(int $discountId)
    {
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
                $response->message = 'К сожалению, все промокоды разобраны.';
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
                $response->message = 'В сутки имеется возможность приобретать только один промокод.';
                return $this->asJson($response);
            }

            $order = new DiscountOrder([
                'user_id' => Yii::$app->user->id,
                'discount_id' => $discount->id,
                'date_buy' => time(),
                'promo_code' => (string) mt_rand(1000, 9999),
                'pin_code' => null,
                'status_promo' => Discounts::STATUS['active'],
            ]);

            if ($order->save()) {
                $discount->updateCounters(['count_orders' => 1]);
            }

            $response->redirectUrl = Url::to(['user/get-promocodes']);
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
                ->with('hasLike')
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