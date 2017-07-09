<?php

namespace app\controllers;

use app\components\MailSender;
use app\models\LoginModel;
use app\models\User;
use Yii;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use app\components\MainController;
use yii\web\NotFoundHttpException;

class SiteController extends MainController
{


    public function actionIndex()
    {

        return $this->render('index');
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            throw new NotFoundHttpException('Cтраница не найдена');
        }
        $model = new LoginModel();
        $model->scenario = LoginModel::SCENARIO_LOGIN;

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->asJson([
                'success' => true,
                'redirect' => Yii::$app->getHomeUrl()
            ]);
        }

        return $this->renderAjax('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionRegister()
    {
        if (!\Yii::$app->user->isGuest) {
            throw new NotFoundHttpException('Cтраница не найдена');
        }

        $model = new LoginModel();
        $model->scenario = LoginModel::SCENARIO_REGISTER;

        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->registerUser()) {
                if (Yii::$app->getUser()->login($user, Yii::$app->params['user.loginDuration'])) {

                    MailSender::sendConfirmMessage();
                    return $this->asJson([
                        'success' => true,
                        'redirect' => Yii::$app->getHomeUrl()
                    ]);
                }
            }
        }
        return $this->renderAjax('register', [
            'model' => $model,
        ]);

    }

    public function actionPasswordRecovery()
    {
        if (!\Yii::$app->user->isGuest) {
            throw new NotFoundHttpException('Cтраница не найдена');
        }

        $model = new LoginModel();
        $model->scenario = LoginModel::SCENARIO_PASSWORD_RECOVERY;
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->getUserForResetPassword()) {

                MailSender::sendPasswordResetMessage($user);
                return $this->asJson([
                    'success' => true,
                    'redirect' => Yii::$app->getHomeUrl()
                ]);
            }
        }

        return $this->renderAjax('password-recovery', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword(string $token)
    {
        if(empty($token) || !($user = User::findByPasswordResetToken($token))){
            throw new BadRequestHttpException('Неверный токен для восстановления пароля');
        }
        $model = new LoginModel();
        $model->scenario = LoginModel::SCENARIO_PASSWORD_RESET_FORM;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if($user->resetPassword($model->password)) {
                return $this->goHome();
            }
        }
        return $this->render('password-reset-form', [
            'model' => $model
        ]);
    }

    public function actionConfirmAccount(string $token){

        if(empty($token) || !($user = User::findIdentityByAccessToken($token))){
            throw new BadRequestHttpException('Неверный токен подтверждения');
        }
        $user->confirmAccount();
        return $this->goHome();
    }

}
