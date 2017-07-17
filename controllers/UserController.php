<?php

namespace app\controllers;

use app\components\MailSender;
use app\components\MainController;
use app\models\City;
use app\models\uploads\UploadUserPhoto;
use app\models\User;
use app\models\UserSettings;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class UserController extends MainController
{

    public function actionIndex($id)
    {
        return $this->render('index');
    }

    public function actionSettings()
    {
        if(Yii::$app->user->isGuest) {
            throw new NotFoundHttpException('Cтраница не найдена');
        }
        $user = Yii::$app->user->identity;
        $model = new UserSettings();
        $model->setUser($user);
        if($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->changePassword();
            if($model->changeEmail()) {
                $mail = new MailSender($model->getUser());
                $mail->sendConfirmMessage('confirmEmail');
            }
            if(!$model->hasErrors()) {
                $model->saveSettings();
                return $this->redirect(['user/settings']);
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
            if ($model->upload()) {
                return $this->asJson([
                    'success' => true,
                    'pathToPhoto' => Yii::$app->user->identity->getPhoto() . '?' . time(),
                ]);
            } else {
                return $this->asJson([
                    'success' => false,
                    'errors' => $model->getErrors(),
                ]);
            }
        }
    }

    public function actionConfirmAccount(string $token)
    {
        $id = Yii::$app->security->decryptByKey($token, Yii::$app->params['security.encryptionKey']);
        if($id === false || !($user = User::findOne((int)$id)) || $user->isConfirmed()){
            throw new BadRequestHttpException('Неверный токен подтверждения');
        }
        if($user->confirmEmail()) {
            //TODO congrats message
        }

        return $this->goHome();
    }
}
