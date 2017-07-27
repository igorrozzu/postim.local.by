<?php

namespace app\controllers;

use app\components\cardsPlaceWidget\CardsPlaceWidget;
use app\components\cardsReviewsWidget\CardsReviewsWidget;
use app\components\MainController;
use app\models\City;
use app\models\Posts;
use app\models\PostsSearch;
use app\models\Reviews;
use app\models\ReviewsSearch;
use app\models\TempEmail;
use app\models\uploads\UploadUserPhoto;
use app\models\User;
use app\models\UserSettings;
use Yii;
use app\components\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class UserController extends MainController
{

    public function actionIndex($id)
    {
        $user = User::find()
            ->with(['userInfo', 'city', 'socialBindings'])
            ->where(['tbl_users.id' => $id])
            ->one();

        $searchModel = new ReviewsSearch();
        $pagination = new Pagination(['pageSize' => 5, 'page' => 0]);
        $loadTime = time();
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
            'feedReviews' => $feedReviews
        ]);
    }

    public function actionSettings()
    {
        if(Yii::$app->user->isGuest) {
            throw new NotFoundHttpException('Cтраница не найдена');
        }
        $user = Yii::$app->user->identity;
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
            $pagination = new Pagination([
                'pageSize' => Yii::$app->request->get('per-page', 5),
                'page' => Yii::$app->request->get('page', 0),
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
                        'user-id' => Yii::$app->request->get('id'),
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
            $pagination = new Pagination([
                'pageSize' => Yii::$app->request->get('per-page', 4),
                'page' => Yii::$app->request->get('page', 1) - 1,
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
                        'load-time' => $loadTime
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
}
