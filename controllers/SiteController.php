<?php

namespace app\controllers;

use app\components\cardsNewsWidget\CardsNewsWidget;
use app\components\cardsPlaceWidget\CardsPlaceWidget;
use app\components\cardsReviewsWidget\CardsReviewsWidget;
use app\components\MailSender;
use app\components\Pagination;
use app\models\Feedback;
use app\models\LoginModel;
use app\models\News;
use app\models\Posts;
use app\models\PostsSearch;
use app\models\Reviews;
use app\models\ReviewsComplaint;
use app\models\ReviewsLike;
use app\models\ReviewsSearch;
use app\models\search\NewsSearch;
use app\models\TempUser;
use app\models\User;
use linslin\yii2\curl\Curl;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\data\SqlDataProvider;
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


			if(Yii::$app->request->post('id',false)){
				$reviews = Reviews::find()
					->where(['id'=>Yii::$app->request->post('id',false)])
					->one();
				$reviews->setScenario(Reviews::$SCENARIO_EDIT);
			}else{
				$reviews = new Reviews();
				$reviews->setScenario(Reviews::$SCENARIO_ADD);
			}

			if($reviews->load( Yii::$app->request->post(),'reviews')){
				if($reviews->save()){
					$response->success = true;
					$response->message = 'Ваш отзыв успешно добавлен';

					if($reviews->getScenario()==Reviews::$SCENARIO_EDIT){

						$query = Reviews::find()
							->joinWith(['user.userInfo'])
							->innerJoinWith(['post'])
							->with('officialAnswer')
							->where([Reviews::tableName().'.id'=>$reviews->id]);

						$dataProvider =  new ActiveDataProvider(['query'=>$query]);
						$response->html =  \app\components\cardsReviewsWidget\CardsReviewsWidget::widget([
							'dataProvider' => $dataProvider,
							'settings'=>[
								'show-more-btn' => false,
								'without_header'=>true
							]
						]);
						$response->message = 'Ваш отзыв успешно обновлен';
					}

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

	public function actionAddRemoveLikeReviews(int $id){

		$response = new \stdClass();
		$response->status='OK';
		Yii::$app->response->format = Response::FORMAT_JSON;

		if(!Yii::$app->user->isGuest){
			$reviews = Reviews::find()->with('hasLike')->where(['id'=>$id])->one();
			if($reviews && !$reviews->is_like){
				if($reviews->updateCounters(['like' => 1])){
					$reviewsLike = new ReviewsLike(['reviews_id'=>$id,'user_id'=>Yii::$app->user->getId()]);
					$reviewsLike->save();
					$response->status='add';
				}
			}else{
				if($reviews->updateCounters(['like' => -1])){
					$reviews->hasLike->delete();
					$response->status='remove';
				}
			}

			$response->count=$reviews->like;
		}else{
			$response->status='error';
			$response->message = 'Незарегистрированные пользователи не могут оценивать отзовы';
		}

		return $response;
	}

	public function actionComplainReviews(){
		$response = new \stdClass();
		$response->status='OK';
		$response->message='Спасибо, что помогаете!<br>Ваша жалоба будет рассмотрена модераторами';
		Yii::$app->response->format = Response::FORMAT_JSON;

		if(!Yii::$app->user->isGuest){
			$reviews_id =Yii::$app->request->post('id',null);
			$message = Yii::$app->request->post('message',null);

			$reviewsComplaint = new ReviewsComplaint(['reviews_id' => $reviews_id,
				'message'=>$message,
				'user_id'=>Yii::$app->user->getId()
			]);

			if ($reviewsComplaint->validate() && $reviewsComplaint->save()) {
				return $response;
			} else {
				$name_attribute = key($reviewsComplaint->getErrors());
				$response->status = 'error';
				$response->message = $reviewsComplaint->getFirstError($name_attribute);
			}
			return $response;
		}
	}

	public function actionVseOtzyvy()
	{
		$request = Yii::$app->request;
		$_GET['type'] = $request->get('type', 'all');
		$searchModel = new ReviewsSearch();
		$pagination = new Pagination([
			'pageSize' => $request->get('per-page', 8),
			'page' => $request->get('page', 1) - 1,
			'route'=>Yii::$app->request->getPathInfo(),
			'selfParams'=> [
				'region' => true,
				'type' => true,
			],
		]);
		$loadTime = $request->get('loadTime', time());

		$dataProvider = $searchModel->search(
			$request->queryParams,
			$pagination,
			$loadTime
		);

		$h1 = '';
		$breadcrumbParams = $this->getParamsForBreadcrumbReviews($h1);

		if($request->isAjax && !$request->get('_pjax',false)) {
			return CardsReviewsWidget::widget([
				'dataProvider' => $dataProvider,
				'settings' => [
					'show-more-btn' => true,
					'replace-container-id' => 'feed-all-reviews',
					'load-time' => $loadTime,
				]
			]);
		} else {
			return $this->render('feed-all-reviews', [
				'dataProvider' => $dataProvider,
				'loadTime' => $loadTime,
				'h1'=>$h1,
				'breadcrumbParams'=>$breadcrumbParams,
				'type' => $request->queryParams['type'],
			]);
		}
	}

	private function getParamsForBreadcrumbReviews(&$h1){
		$breadcrumbParams=[];

		$currentUrl = Yii::$app->getRequest()->getHostInfo();
		$breadcrumbParams[] = [
			'name' => ucfirst(Yii::$app->getRequest()->serverName),
			'url_name' => $currentUrl,
			'pjax' => 'class="main-header-pjax a"'
		];

		if($city = Yii::$app->request->get('city')){
			$currentUrl=$currentUrl.'/'.$city['url_name'];
			$breadcrumbParams[]=[
				'name'=>$city['name'],
				'url_name'=>$currentUrl,
				'pjax'=>'class="main-pjax a"'
			];
		}

		$currentUrl=$currentUrl.'/'.'otzyvy';
		$name='Все отзывы';
		$h1 = 'Все отзывы в '.Yii::t('app/locativus','Беларусь');
		if($city = Yii::$app->request->get('city')){
			$name ='Все отзывы';
			$h1 = 'Все отзывы в '.Yii::t('app/locativus',$city['name']);
		}

		$breadcrumbParams[]=[
			'name'=>$name,
			'url_name'=>$currentUrl,
			'pjax'=>'class="main-pjax a"'
		];




		return $breadcrumbParams;
	}

	public function actionSearchAutoComplete(string $text){

        $model = new PostsSearch();
        $modelNews = new NewsSearch();

        $dataAutoComplete = $model->getAutoComplete($text);
        $dataAutoCompleteNews = $modelNews->getAutoComplete($text);

        return $this->renderAjax('__search_auto_complete.php',
            [
                'dataAutoComplete' => $dataAutoComplete,
                'dataAutoCompleteNews' => $dataAutoCompleteNews
            ]
        );

    }

    public function actionSearch(string $text){

	    $model  = null;
        $dataProvider = null;
        $widget = null;
        $widget_params = null;

        $pagination = new Pagination([
            'pageSize' => Yii::$app->request->get('per-page', 8),
            'page' => Yii::$app->request->get('page', 1) - 1,
            'route'=>Yii::$app->request->getPathInfo(),
            'selfParams'=> [
                'text'=>true,
                'type_feed' => true,
            ],
        ]);
        $loadTime = Yii::$app->request->get('loadTime', time());


        $modelPlace = new PostsSearch();
        $widgetPlace = CardsPlaceWidget::className();
        $widget_paramsPlace = [
            'dataprovider' => null,
            'settings'=>[
                'show-more-btn'=>true,
                'replace-container-id' => 'feed-posts',
                'load-time' => $loadTime,

            ]
        ];

        $modelNews = new NewsSearch();
        $widgetNews = CardsNewsWidget::className();
        $widget_paramsNews = [
            'dataprovider' => null,
            'settings' =>
                [
                    'replace-container-id' => 'feed-news',
                    'load-time' => $loadTime
                ]
        ];

        $widget_paramsPlace['dataprovider'] = $modelPlace->search(
            Yii::$app->request->queryParams,
            $pagination,
            ['date' => SORT_DESC],
            $loadTime
        );

        $widget_paramsNews['dataprovider'] = $modelNews->search(
            Yii::$app->request->queryParams,
            $pagination,
            ['date' => SORT_DESC],
            $loadTime
        );

	    if(Yii::$app->request->isAjax && !Yii::$app->request->get('_pjax',false)){
	        if(Yii::$app->request->get('type_feed','place')){
                echo $widgetPlace::widget($widget_paramsPlace);
            }else{
                echo $widgetNews::widget($widget_paramsNews);
            }

        }else{
            return $this->render('__search_feeds.php',
                [
                    'widgetPlace' => $widgetPlace,
                    'widget_paramsPlace' => $widget_paramsPlace,
                    'widgetNews' => $widgetNews,
                    'widget_paramsNews' => $widget_paramsNews
                ]
            );
        }

    }

    public function actionFeedback(){

        $feedBack = new Feedback();
        $toastMessage = null;

        if(Yii::$app->request->isPost){
            if($feedBack->load(Yii::$app->request->post()) && $feedBack->sendSubject()) {
                $toastMessage = [
                    'type' => 'success',
                    'message' => 'Сообщение отправлено',
                ];
            }else{
                $toastMessage = [
                    'type' => 'error',
                    'message' => 'Произошла ошибка при отправке',
                ];
            }
        }

        return $this->render('feedBack',['feedBack'=>$feedBack,'toastMessage'=>$toastMessage]);
    }

}
