<?php

namespace app\controllers;

use app\components\ImageHelper;
use app\components\MailSender;
use app\components\MainController;
use app\components\social\attributes\SocialAuthAttributes;
use app\components\social\attributes\SocialAuthAttributesFactory;
use app\models\LoginModel;
use app\models\SocialAuth;
use app\models\SocialRegister;
use app\models\TempSocialUser;
use app\models\User;
use Yii;
use yii\base\Exception;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class SocialAuthController extends MainController
{
    public function actions()
    {
        return [
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }

    public function beforeAction($action)
    {

        $baseUrl = Url::toRoute('/', 'https');

        if ($action->id === 'auth' && stripos(Yii::$app->request->referrer, $baseUrl) !== false) {

            Yii::$app->session->setFlash('beforeAuthReferrer', Yii::$app->request->referrer);

        }

        return parent::beforeAction($action);
    }

    public function onAuthSuccess($client)
    {
        $referrer = Yii::$app->session->hasFlash('beforeAuthReferrer') ? Yii::$app->session->getFlash('beforeAuthReferrer') : Yii::$app->request->referrer;

        try {
            $attributes = $client->getUserAttributes();
            $socialAuthFactory = new SocialAuthAttributesFactory();
            $attrClient = $socialAuthFactory->getSocialAttributes($client->getId(), $attributes);
            $model = new SocialRegister();

            $auth = SocialAuth::find()->where([
                'source' => $attrClient->getSocialName(),
                'source_id' => $attrClient->getSocialId(),
            ])->one();

            if (Yii::$app->user->isGuest) {
                if ($auth) {
                    $user = $auth->user;
                    $user->setTimezoneOffset();
                    Yii::$app->user->login($user, Yii::$app->params['user.loginDuration']);
                } else {
                    $email = $attrClient->getEmail();
                    if ($email === null) {
                        if ($tempUser = $model->createTempSocialUser($attrClient)) {
                            $key = Yii::$app->security->encryptByKey($tempUser->id,
                                Yii::$app->params['security.encryptionKey']);
                            return $this->redirect(['social-auth/set-required-fields', 'key' => $key]);
                        }
                    } else {

                        if ($user = User::findOne(['email' => $email])) {
                            $model->createSocialBinding($attrClient, $user->id);
                            Yii::$app->user->login($user, Yii::$app->params['user.loginDuration']);

                        } else {
                            $user = $model->createUser($attrClient);
                            if ($user) {
                                Yii::$app->user->login($user, Yii::$app->params['user.loginDuration']);
                            }
                        }
                        return $this->redirect(Url::toRoute([$referrer, 'NewUser' => 'new'], 'https'));
                    }
                }
            } else { // Пользователь уже зарегистрирован
                if (!$auth) { // добавляем внешний сервис аутентификации
                    $model->createSocialBinding($attrClient, Yii::$app->user->getId());
                } else {
                    Yii::$app->session->setFlash('toastMessage', $toastMessage = [
                        'type' => 'error',
                        'message' => 'Этот аккаунт уже привязан к другому профилю на Постим',
                    ]);
                }
                return $this->redirect(['user/settings']);
            }
        } catch (Exception $e) {
            return $this->redirect($referrer);
        }

        return $this->redirect($referrer);
    }

    public function actionSetRequiredFields(string $key)
    {
        $id = Yii::$app->security->decryptByKey($key, Yii::$app->params['security.encryptionKey']);
        if ($id === false || !($tempUser = TempSocialUser::findOne((int)$id)) || $tempUser->isMailSent()) {
            throw new NotFoundHttpException();
        }
        $model = new SocialRegister();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->setRequiredFields($tempUser);

            Yii::$app->mailer->compose(['html' => 'socialConfirmEmail'], ['user' => $tempUser])
                ->setFrom([Yii::$app->params['mail.supportEmail'] => 'Postim.by'])
                ->setTo($tempUser->email)
                ->setSubject('Подтверждение аккаунта на Postim.by')
                ->send();

            Yii::$app->session->setFlash('render-form-view', 'confirm-email');
            return $this->goHome();
        }

        $params = $this->getParamsForMainPage();
        $this->view->params['form-message'] = $this->renderPartial('required-fields-form', [
            'model' => $model,
            'name' => $model->name ?? $tempUser->name,
        ]);
        return $this->render('//site/index', $params);
    }

    public function actionConfirmAccount(string $token, string $hash)
    {
        $id = Yii::$app->security->decryptByKey($token, Yii::$app->params['security.encryptionKey']);
        if ($id === false || !($tempUser = TempSocialUser::findOne(['id' => $id, 'hash' => $hash]))) {
            Yii::$app->session->setFlash('render-form-view', 'failed-confirm-account');
            return $this->goHome();
        }
        $model = new SocialRegister();
        if ($model->createUser($tempUser)) {
            $tempUser->delete();
            Yii::$app->session->setFlash('render-form-view', 'success-confirmation');
        }
        return $this->goHome();
    }
}