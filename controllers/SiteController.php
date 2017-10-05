<?php

namespace app\controllers;

use app\components\MailSender;
use app\models\LoginModel;
use app\models\News;
use app\models\Posts;
use app\models\Reviews;
use app\models\TempUser;
use app\models\User;
use linslin\yii2\curl\Curl;
use Yii;
use yii\base\Exception;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use app\components\MainController;
use yii\web\NotFoundHttpException;
use yii\web\Response;

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

    public function actionConfirmAccount(string $token, string $hash)
    {
        $id = Yii::$app->security->decryptByKey($token, Yii::$app->params['security.encryptionKey']);
        if($id === false || !($tempUser = TempUser::findOne(['id' => $id, 'hash' => $hash]))){
            Yii::$app->session->setFlash('render-form-view', 'failed-confirm-account');
            return $this->goHome();
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

    public function actionGetFormComplaint(){
        return $this->renderPartial('form_complaint');
    }

    public function actionGetCoordsByAddress(){
    	$address = Yii::$app->request->get('address');
    	$address = str_replace(' ','+',$address);



		Yii::$app->response->format = Response::FORMAT_JSON;
		$response = new \stdClass();
		$response->error = true;
		$response->zoom = 12;
		if(mb_stripos($address,'область')){
			$response->zoom = 7;
		}

		if ($address) {
			$curl = new Curl();
			$response->data = $curl->get('http://maps.googleapis.com/maps/api/geocode/json?address=Беларусь+'.$address);
			$response->data = Json::decode($response->data);
			if($response->data['status'] == 'OK'){
				$response->error = false;
				$response->location = $response->data['results'][0]['geometry']['location'];
			}
		}

		return $response;

	}

	public function actionSaveReviews(){

		$response = new \stdClass();
		$response->success = false;
		$response->message = '';
		Yii::$app->response->format = Response::FORMAT_JSON;

		if(!Yii::$app->user->isGuest){
			$reviews = new Reviews();

			if($reviews->load( Yii::$app->request->post(),'reviews')){
				$reviews->like = 0;
				$reviews->date = time();
				$reviews->user_id = Yii::$app->user->getId();
				if($reviews->save()){
					$response->success = true;
					$response->message = 'Ваш отзыв успешно добавлен';
				}else{
					$name_attribute = key($reviews->getErrors());
					$response->message = $reviews->getFirstError($name_attribute);
				}

			}
		}else{
			$response->message = 'Незарегистрированные пользователи не могут оставлять отзовы';
		}

		return $response;
	}

}
