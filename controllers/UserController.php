<?php

namespace app\controllers;

use app\components\cardsNewsWidget\CardsNewsWidget;
use app\components\cardsPlaceWidget\CardsPlaceWidget;
use app\components\cardsPromoWidget\CardsPromoWidget;
use app\components\cardsReviewsWidget\CardsReviewsWidget;
use app\components\MainController;
use app\components\orderStatisticsWidget\OrderStatisticsWidget;
use app\components\user\FavoritesFeedsHelper;
use app\models\City;
use app\models\entities\DiscountOrder;
use app\models\entities\Gallery;
use app\models\entities\OwnerPost;
use app\models\moderation_post\PostsModeration;
use app\models\moderation_post\PostsModerationSearch;
use app\models\PostsSearch;
use app\models\Region;
use app\models\ReviewsSearch;
use app\models\search\DiscountOrderSearch;
use app\models\search\GallerySearch;
use app\models\search\NewsSearch;
use app\models\TempEmail;
use app\models\uploads\UploadUserPhoto;
use app\models\User;
use app\models\UserSettings;
use Yii;
use app\components\Pagination;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class UserController extends MainController
{

    public function actionIndex(int $id, int $photo_id = null, int $review_id = null)
    {
        $user = User::find()
            ->with(['userInfo', 'city', 'socialBindings'])
            ->joinWith(['isOwner'])
            ->where(['tbl_users.id' => $id])
            ->one();

        $searchModel = new ReviewsSearch();
        $pagination = new Pagination([
            'pageSize' => 5,
            'page' => 0,
            'route' => Url::to(['user/reviews']),
            'selfParams'=> [
                'id' => true,
            ],
        ]);
        $loadTime = time();
        $_GET['id'] = $id;
        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            $pagination,
            $loadTime
        );
        $feedReviews = $this->renderPartial('feed-reviews', [
            'user' => $user,
            'dataProvider' => $dataProvider,
            'loadTime' => $loadTime,
        ]);

        return $this->render('index', [
            'user' => $user,
            'feedReviews' => $feedReviews,
            'profilePhotoCount' => Gallery::getProfilePhotoCount($user->id),
            'profilePreviewPhoto' => Gallery::getProfilePreviewPhoto($user->id, 4),
            'initPhotoSliderParams' => [
                'photoId' => $photo_id,
                'reviewId' => $review_id,
            ]
        ]);
    }

    public function actionSettings()
    {
        if(Yii::$app->user->isGuest) {
            throw new NotFoundHttpException('Cтраница не найдена');
        }
        $user = User::find()
            ->innerJoinWith('userInfo')
            ->where([User::tableName().'.id' => Yii::$app->user->id])
            ->one();
        $model = new UserSettings();
        $model->setUser($user);
        $toastMessage = null;

        if($model->load(Yii::$app->request->post())) {
            $validation = $model->validate();
            if($model->changePassword() && $validation) {
                $model->saveSettings();
                $model->resetPasswordFields();
                $toastMessage = [
                    'type' => 'success',
                    'message' => 'Изменения сохранены',
                ];
            } else {
                $toastMessage = [
                    'type' => 'error',
                    'message' => 'Произошла ошибка при сохранении настроек',
                ];
            }
        }

        $socialBindings = $user->socialBindings;
        if(!$cities = Yii::$app->cache->get('cities_for_user_settings_form')){
            $cities = City::find()
                ->select(['id', 'name'])
                ->orderBy(['name' => SORT_ASC])
                ->asArray()
                ->all();
            Yii::$app->cache->add('cities_for_user_settings_form', $cities,3600);
        }
        $userCityName = null;
        if(isset($model->cityId) || $user->isCityDefined()) {
            $userCityName =  City::removeCityById($cities, $model->cityId ?? $user->city_id);
        }
        return $this->render('settings-form', [
            'model' => $model,
            'socialBindings' => $socialBindings,
            'cities' => $cities,
            'userCityName' => $userCityName,
            'toastMessage' => $toastMessage ?? Yii::$app->session->getFlash('toastMessage'),
        ]);
    }

    public function actionChangeEmail()
    {
        if (\Yii::$app->user->isGuest) {
            throw new NotFoundHttpException('Cтраница не найдена');
        }
        $model = new UserSettings();
        $model->scenario = UserSettings::SCENARIO_EMAIL_RESET;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if($tempData = $model->createTempEmailData()) {
                Yii::$app->mailer->compose(['html' => 'confirmEmail'], ['data' => $tempData])
                    ->setFrom([Yii::$app->params['mail.supportEmail'] => 'Postim.by'])
                    ->setTo($tempData->email)
                    ->setSubject('Подтверждение email-адреса на Postim.by')
                    ->send();
                return $this->renderAjax('confirm-email');
            }
        }

        return $this->renderAjax('change-email-form', [
            'model' => $model,
        ]);
    }

    public function actionUploadPhoto()
    {
        if(Yii::$app->user->isGuest) {
            throw new NotFoundHttpException('Cтраница не найдена');
        }

        if(Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $model = new UploadUserPhoto();
            $model->imageFile = UploadedFile::getInstanceByName('user-photo');
            $user = Yii::$app->user->identity;
            if ($model->upload($user)) {
                return $this->asJson([
                    'success' => true,
                    'pathToPhoto' => $user->getPhoto(),
                    'message' => 'Изменения сохранены',
                ]);
            } else {
                return $this->asJson([
                    'success' => false,
                    'message' => 'Изображение должно быть не меньше, чем 300 x 300 ' .
                        'пикселей в формате JPG, GIF или PNG. Макс. размер файла: 5 МБ.'
                ]);
            }
        }
    }

    public function actionConfirmAccount(string $id, string $token)
    {
        $tempData = TempEmail::findOne(['user_id' => $id, 'hash' => $token]);

        if(Yii::$app->user->isGuest) {
            if(isset($tempData)) {
                $tempData->delete();
            }
        } else {
            if(isset($tempData)) {
                $user = Yii::$app->user->identity;
                if($user->changeEmail($tempData->email)) {
                    $tempData->delete();
                    Yii::$app->session->setFlash('toastMessage', $toastMessage = [
                        'type' => 'success',
                        'message'=> 'Изменения сохранены',
                    ]);
                }
                return $this->redirect(['user/settings']);
            }
        }
        Yii::$app->session->setFlash('render-form-view', 'failed-confirm-email');
        return $this->goHome();
    }

    public function actionReviews()
    {
        if(Yii::$app->request->isAjax) {
            $searchModel = new ReviewsSearch();
            $_GET['id'] = Yii::$app->request->get('id', Yii::$app->user->id);
            $pagination = new Pagination([
                'pageSize' => Yii::$app->request->get('per-page', 5),
                'page' => Yii::$app->request->get('page', 1) - 1,
                'selfParams'=> [
                    'id' => true,
                ],
            ]);
            $loadTime = Yii::$app->request->get('loadTime', time());
            $dataProvider = $searchModel->search(
                Yii::$app->request->queryParams,
                $pagination,
                $loadTime
            );

            if(!Yii::$app->request->get('_pjax',false) ){
                return CardsReviewsWidget::widget([
                    'dataProvider' => $dataProvider,
                    'settings' => [
                        'show-more-btn' => true,
                        'replace-container-id' => 'feed-reviews',
                        'load-time' => $loadTime,
                    ]
                ]);
            }else{
                $user = User::find()
                    ->with(['userInfo'])
                    ->where(['tbl_users.id' => Yii::$app->request->get('id')])
                    ->one();
                return $this->renderPartial('feed-reviews', [
                    'user' => $user,
                    'dataProvider' => $dataProvider,
                    'loadTime' => $loadTime,
                ]);
            }
        }
    }

    public function actionPlaces()
    {
        if(Yii::$app->request->isAjax) {
            $searchModel = new PostsSearch();
            if(Yii::$app->request->get('moderation', null) === null ? false : true){
				$searchModel = new PostsModerationSearch();
			}
            $pagination = new Pagination([
                'pageSize' => Yii::$app->request->get('per-page', 8),
                'page' => Yii::$app->request->get('page', 1) - 1,
				'selfParams'=>[
					'id'=>true,
					'moderation'=>true
				]
            ]);
            $loadTime = Yii::$app->request->get('loadTime', time());
            $dataProvider = $searchModel->search(
                Yii::$app->request->queryParams,
                $pagination,
                PostsSearch::getSortArray('new'),
                $loadTime
            );

            if(!Yii::$app->request->get('_pjax',false) ){
                return  CardsPlaceWidget::widget([
                    'dataprovider' => $dataProvider,
                    'settings' => [
                        'show-more-btn' => true,
                        'replace-container-id' => 'feed-posts',
                        'load-time' => $loadTime,
						'moderation' => Yii::$app->request->get('moderation', null) === null ? false : true,
                    ]
                ]);
            }else{
                $user = User::find()
                    ->with(['userInfo'])
                    ->where(['tbl_users.id' => Yii::$app->request->get('id')])
                    ->one();
                return $this->renderPartial('feed-places', [
                    'user' => $user,
                    'dataProvider' => $dataProvider,
                    'loadTime' => $loadTime,
                    'moderation' => Yii::$app->request->get('moderation', null)
                ]);
            }
        }
    }

    public function actionGetPromocodes()
    {
        if (Yii::$app->user->isGuest) {
            throw new NotFoundHttpException('Cтраница не найдена');
        }

        $searchModel = new DiscountOrderSearch();
        $request = Yii::$app->request;
        $pagination = new Pagination([
            'pageSize' => $request->get('per-page', 8),
            'page' => $request->get('page', 1) - 1,
            'selfParams'=> [
                'status' => true,
                'type' => true
            ],
        ]);
        $loadTime = $request->get('loadTime', time());
        $_GET['status'] = $_GET['status'] ?? 'active';
        $_GET['type'] = $_GET['type'] ?? 'promocode';
        $dataProvider = $searchModel->search(
            $request->queryParams,
            $pagination,
            $loadTime
        );

        if($request->isAjax && !$request->get('_pjax',false)) {
            return CardsPromoWidget::widget([
                'dataProvider' => $dataProvider,
                'settings' => [
                    'show-more-btn' => true,
                    'replace-container-id' => 'feed-promo',
                    'load-time' => $loadTime,
                    'show-more-btn-text' => 'Показать больше промокодов',
                    'not-found-text' => 'Промокодов не найдено.',
                    'status' => $request->queryParams['status'],
                ]
            ]);
        } else {
            $breadcrumbParams = $this->getParamsForBreadcrumb();
            $breadcrumbParams[] = [
                'name' => 'Мои промокоды',
                'url_name' => Url::to(['user/get-promocodes']),
                'pjax' => 'class="main-pjax a"'
            ];

            return $this->render('feed-promo', [
                'breadcrumbParams' => $breadcrumbParams,
                'dataProvider' => $dataProvider,
                'loadTime' => $loadTime,
                'status' => $request->queryParams['status']
            ]);
        }
    }

    public function actionSertifikaty()
    {
        $searchModel = new DiscountOrderSearch();
        $request = Yii::$app->request;
        $pagination = new Pagination([
            'pageSize' => $request->get('per-page', 2),
            'page' => $request->get('page', 1) - 1,
            'selfParams'=> [
                'status' => true,
                'type' => true
            ],
        ]);
        $loadTime = $request->get('loadTime', time());
        $_GET['status'] = $_GET['status'] ?? 'active';
        $_GET['type'] = $_GET['type'] ?? 'certificate';
        $dataProvider = $searchModel->search(
            $request->queryParams,
            $pagination,
            $loadTime
        );

        if($request->isAjax && !$request->get('_pjax',false)) {
            return CardsPromoWidget::widget([
                'dataProvider' => $dataProvider,
                'settings' => [
                    'show-more-btn' => true,
                    'replace-container-id' => 'feed-certificate',
                    'load-time' => $loadTime,
                    'show-more-btn-text' => 'Показать больше сертификатов',
                    'not-found-text' => 'Вы пока не купили ни одного сертификата.',
                ]
            ]);
        } else {
            return $this->render('feed-certificate', [
                'dataProvider' => $dataProvider,
                'loadTime' => $loadTime,
                'status' => $request->queryParams['status']
            ]);
        }
    }

    public function actionIzbrannoe()
    {
        $request = Yii::$app->request;

        $_GET['favorite'] = $request->get('favorite', 'posts');
        $_GET['favorite_id'] = $request->get('favorite_id', Yii::$app->user->id);

        $helper = new FavoritesFeedsHelper($_GET['favorite']);

        $searchModel = $helper->getModel();
        $pagination = new Pagination([
            'pageSize' => $request->get('per-page', 6),
            'page' => $request->get('page', 1) - 1,
            'selfParams'=> [
                'favorite' => true,
                'favorite_id' => true,
            ],
        ]);

        $loadTime = $request->get('loadTime', time());
        $dataProvider = $searchModel->searchFavorites(
            $request->queryParams,
            $pagination,
            $loadTime
        );

        $breadcrumbParams = $this->getParamsForBreadcrumb();
        $breadcrumbParams[] = [
            'name' => 'Избранное',
            'url_name' => Yii::$app->request->getUrl(),
            'pjax' => 'class="main-pjax a"'
        ];

        $widgetClassName = $helper->getWidgetClassName();

        if ($request->isAjax && !$request->get('_pjax',false)) {
            return $widgetClassName::widget([
                'dataprovider' => $dataProvider,
                'settings' => [
                    'show-more-btn' => true,
                    'replace-container-id' => 'feed-favorites',
                    'load-time' => $loadTime,
                ],
            ]);
        } else {
            return $this->render('feed-favorites', [
                'dataProvider' => $dataProvider,
                'loadTime' => $loadTime,
                'feed' => $_GET['favorite'],
                'widgetClassName' => $widgetClassName,
                'widgetWrapAttributes' => $helper->getWidgetWrapAttributes(),
                'breadcrumbParams' => $breadcrumbParams
            ]);
        }
    }

    private function getParamsForBreadcrumb()
    {
        $breadcrumbParams=[];

        $currentUrl = Yii::$app->getRequest()->getHostInfo();
        $breadcrumbParams[] = [
            'name' => ucfirst(Yii::$app->getRequest()->serverName),
            'url_name' => $currentUrl,
            'pjax' => 'class="main-header-pjax a"'
        ];

        if($city = Yii::$app->city->getSelected_city()){
            if($city['url_name']){
                $currentUrl=$currentUrl.'/'.$city['url_name'];
                $breadcrumbParams[]=[
                    'name'=>$city['name'],
                    'url_name'=>$currentUrl,
                    'pjax'=>'class="main-pjax a"'
                ];
            }
        }

        return $breadcrumbParams;
    }

    public function actionOrderCertificates()
    {
        if (!Yii::$app->user->isOwnerPost()) {
            throw new NotFoundHttpException('Cтраница не найдена');
        }

        $searchModel = new DiscountOrderSearch();
        $request = Yii::$app->request;
        $pagination = new Pagination([
            'pageSize' => $request->get('per-page', 2),
            'page' => $request->get('page', 1) - 1,
            'selfParams'=> [
                'status' => true,
                'type' => true,
                'order_time' => true,
                'promo_code' => true,
            ],
        ]);
        $loadTime = $request->get('loadTime', time());
        $_GET['status'] = $_GET['status'] ?? 'all';
        $_GET['type'] = $_GET['type'] ?? 'certificate';
        $dataProvider = $searchModel->statisticsSearch(
            $request->queryParams,
            $pagination,
            $loadTime
        );

        if($request->isAjax && !$request->get('_pjax',false)) {
            return OrderStatisticsWidget::widget([
                'dataProvider' => $dataProvider,
                'settings' => [
                    'show-more-btn' => true,
                    'replace-container-id' => 'feed-promo',
                    'load-time' => $loadTime,
                    'view-name' => $request->get('only_rows', false) ? 'rows' : 'index',
                    'column-status-view' => 'certificate',
                    'time-range' => $searchModel->getTimeRange(),
                ]
            ]);
        } else {
            $allOrderCount = DiscountOrder::getAllCount(DiscountOrder::TYPE['certificate']);
            $activeOrderCount = DiscountOrder::getActiveCount(DiscountOrder::TYPE['certificate'],
                                DiscountOrder::STATUS['active']);
            return $this->render('statistics-certificate', [
                'dataProvider' => $dataProvider,
                'loadTime' => $loadTime,
                'status' => $request->queryParams['status'],
                'order_time' => $request->queryParams['order_time'] ?? null,
                'timeRange' => $searchModel->getTimeRange(),
                'countItems' => [
                    'all'=> $allOrderCount,
                    'active' => $activeOrderCount,
                    'inactive' => $allOrderCount - $activeOrderCount,
                ]
            ]);
        }
    }

    public function actionGetPhotos()
    {
        if (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $searchModel = new GallerySearch();
            $perPage = (int) $request->get('per-page', 16);

            $pagination = new Pagination([
                'pageSize' => $perPage,
                'page' => $request->get('page', 1) - 1,
                'selfParams'=> [
                    'type' => true,
                    'userId' => true,
                    'photo_id' => true,
                ],
            ]);
            $loadTime = $request->get('loadTime', time());
            $dataProvider = $searchModel->searchProfilePhotos(
                $request->queryParams,
                $pagination,
                $loadTime
            );

            $response = new \stdClass();

            if (isset($request->queryParams['photo_id'])) {
                $count = $searchModel->getProfilePreviewsPhotoCount($loadTime);
                $page = (int) ($count / 16);
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
            foreach ($response->data as $photo) {
                $response->postInfo[] = [
                    'title' => $photo->post->data,
                    'url' => $photo->post->url_name,
                ];
            }
            return $this->asJson($response);
        }
    }

    public function actionConfirmUsedOrder(int $id)
    {
        $response = new \stdClass();
        $response->success = false;

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $order = DiscountOrder::find()
                ->where([
                    'user_id' => Yii::$app->user->getId(),
                    'id' => $id
                ])->one();

            if (!isset($order)) {
                return $this->asJson($response);
            }

            $order->status_promo = DiscountOrder::STATUS['inactive'];
            if ($order->save()) {
                $response->success = true;
            }
        }

        return $this->asJson($response);
    }
}
