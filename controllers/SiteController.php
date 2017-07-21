<?php

namespace app\controllers;

use app\components\MailSender;
use app\models\LoginModel;
use app\models\News;
use app\models\Posts;
use app\models\TempUser;
use app\models\User;
use Yii;
use yii\base\Exception;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use app\components\MainController;
use yii\web\NotFoundHttpException;

class SiteController extends MainController
{

    public function actionIndex()
    {
        $viewName = Yii::$app->session->getFlash('render-form-view');
        if(isset($viewName)) {
            $this->view->params['form-message'] = $this->renderPartial($viewName);
        }

        $params = $this->getParamsForMainPage();
        return $this->render('index', $params);
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

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($user = $model->createTempUser()) {

                Yii::$app->mailer->compose(['html' => 'confirmAccount'], ['user' => $user])
                    ->setFrom([Yii::$app->params['mail.supportEmail'] => 'Postim.by'])
                    ->setTo($user->email)
                    ->setSubject('Подтверждение аккаунта на Postim.by')
                    ->send();
                return $this->renderAjax('confirm-email');
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
                Yii::$app->mailer->compose(['html' => 'passwordReset'], ['user' => $user])
                    ->setFrom([Yii::$app->params['mail.supportEmail'] => 'Postim.by'])
                    ->setTo($user->email)
                    ->setSubject('Смена пароля на Postim.by')
                    ->send();

                return $this->renderAjax('confirm-password-recovery');
            }
        }

        return $this->renderAjax('password-recovery', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword(string $token)
    {
        $user = User::findByPasswordResetToken($token);
        if (!\Yii::$app->user->isGuest) {
            if(isset($user)) {
                $user->resetPasswordToken();
            }
            Yii::$app->session->setFlash('render-form-view', 'failed-auth-password-recovery');
            return $this->goHome();
        } else {
            if(!isset($user)) {
                Yii::$app->session->setFlash('render-form-view', 'failed-password-recovery');
                return $this->goHome();
            }
            $model = new LoginModel();
            $model->scenario = LoginModel::SCENARIO_PASSWORD_RESET_FORM;
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                if ($user->resetPassword($model->password)) {
                    Yii::$app->session->setFlash('render-form-view', 'success-password-recovery');
                    return $this->goHome();
                }
            }

            $params = $this->getParamsForMainPage();
            $this->view->params['form-message'] = $this->renderPartial('password-reset-form', [
                'model' => $model
            ]);
            return $this->render('index', $params);
        }
    }

    public function actionConfirmAccount(string $token)
    {
        $id = Yii::$app->security->decryptByKey($token, Yii::$app->params['security.encryptionKey']);
        if($id === false || !($tempUser = TempUser::findOne((int)$id))){
            throw new BadRequestHttpException('Неверный токен подтверждения');
        }
        $model = new LoginModel();
        if($model->createUser($tempUser)){
            $tempUser->delete();
            Yii::$app->session->setFlash('render-form-view', 'success-confirmation');
        }
        return $this->goHome();
    }

    public function actionSetCity(){

        try{
            $dataCity = Yii::$app->request->get('dataCity',['{"name": "Беларусь","url_name": "\'\'"}']);
            $dataCity = Json::decode($dataCity);

            Yii::$app->city->setCity($dataCity);
            $this->redirect(Yii::$app->request->hostInfo.'/'.Yii::$app->city->Selected_city['url_name']);
        }catch (\yii\base\InvalidParamException $exception){
            $this->goHome();
        }

    }

}
