<?php

namespace app\controllers;

use app\components\cardsNewsWidget\CardsNewsWidget;
use app\components\cardsPlaceWidget\CardsPlaceWidget;
use app\components\cardsReviewsWidget\CardsReviewsWidget;
use app\components\customUrlManager\CityAndCategoryUrlRule;
use app\components\customUrlManager\DiscountsUrlRule;
use app\components\customUrlManager\NewsUrlRule;
use app\components\customUrlManager\ReviewsUrlRule;
use app\components\MailSender;
use app\components\Pagination;
use app\models\entities\Complaints;
use app\models\Feedback;
use app\models\Geocoding;
use app\models\LoginModel;
use app\models\OtherPage;
use app\models\PostsSearch;
use app\models\Reviews;
use app\models\ReviewsLike;
use app\models\ReviewsSearch;
use app\models\search\NewsSearch;
use app\models\TempUser;
use app\models\User;
use linslin\yii2\curl\Curl;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\helpers\Url;
use app\components\MainController;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Request;
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
                'redirect' => Yii::$app->request->referrer,
            ]);
        }
        if(Yii::$app->request->get('with-message',false)){
            return $this->renderPartial('login_whith_message',['model'=>$model]);
        }else{
            return $this->renderPartial('login', [
                'model' => $model,
            ]);
        }

    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->redirect(Yii::$app->request->referrer);
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
        return $this->renderPartial('register', [
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
            return $this->redirect('/?NewUser');
        }
        return $this->goHome();
    }

    public function actionSetCity(){

        try{


            $request = Yii::$app->request;
            $dataCity = Yii::$app->request->get('dataCity',['{"name": "Беларусь","url_name": "\'\'"}']);
            $dataCity = Json::decode($dataCity);

            Yii::$app->city->setCity($dataCity);

            $preUrl = $request->getReferrer();
            $prevPathInfo = preg_replace('/https:?\/\/[^\/]+\//','', $preUrl);

            $preRequest = new Request();
            $preRequest->setUrl($preUrl);
            $preRequest->setPathInfo($prevPathInfo);
            $newUrl = Yii::$app->request->hostInfo.'/'.Yii::$app->city->Selected_city['url_name'];

            $categoryUrlRuler = new CityAndCategoryUrlRule();
            $newsUrlRule = new NewsUrlRule();
            $reviewsUrlRule = new ReviewsUrlRule();
            $discountsUrlRule = new DiscountsUrlRule();

            $isCategory = $categoryUrlRuler->parseRequest(Yii::$app->getUrlManager(),$preRequest);
            if($isCategory && $isCategory[0] != '/site/index'){

                $nameCategory = $isCategory[1]['under_category']['url_name'] ?? $isCategory[1]['category']['url_name'];
                $newUrl = ($dataCity['url_name']?'/'.$dataCity['url_name']:'').'/'.$nameCategory;

                if ($isCategory[0] === '/category/get-discounts') {
                    $newUrl .= '/skidki';
                }

                return $this->redirect($newUrl);
            }

            if($newsUrlRule->parseRequest(Yii::$app->getUrlManager(),$preRequest)){
                $newUrl = ($dataCity['url_name']?'/'.$dataCity['url_name']:'').'/'.'novosti';
                return $this->redirect($newUrl);
            }

            if($reviewsUrlRule->parseRequest(Yii::$app->getUrlManager(),$preRequest)){
                $newUrl = ($dataCity['url_name']?'/'.$dataCity['url_name']:'').'/'.'otzyvy';
                return $this->redirect($newUrl);
            }

            if($discountsUrlRule->parseRequest(Yii::$app->getUrlManager(), $preRequest)){
                $newUrl = ($dataCity['url_name'] ? '/'.$dataCity['url_name'] : '') . '/skidki';
                return $this->redirect($newUrl);
            }

            return $this->redirect($newUrl);
        }catch (\yii\base\InvalidParamException $exception){
            $this->goHome();
        }

    }

    public function actionGetFormComplaint(){
        return $this->renderPartial('form_complaint');
    }

    public function actionGetCoordsByAddress(){

    	$address = Yii::$app->request->get('address');

		Yii::$app->response->format = Response::FORMAT_JSON;
		$response = new \stdClass();
		$response->error = true;
		$response->zoom = 12;
		if(mb_stripos($address,'область')){
			$response->zoom = 7;
		}

		$query = Geocoding::buildQuery($address);
		$geo = Geocoding::find()->where(['query'=>$query])->one();

		if($geo){
		    $response->data = Json::decode($geo->data);
            $response->error = false;
            $response->location = $response->data['results'][0]['geometry']['location'];
        }


		return $response;

	}

	public function actionSearchCoordsByAddress(string $address){

        $curl = new Curl();
        $responseJson = $curl->setGetParams([
            'geocode' => $address,
            'lang' => 'ru_RU',
            'format' => 'json',
            'results' => 1,
        ])
            ->get('https://geocode-maps.yandex.ru/1.x/');
        $response = Json::decode($responseJson);

        $pointsText = $response['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos']??'';

        $points = explode(' ',$pointsText);

        if(is_array($points) && isset($points[1])){

            return $this->asJson(['error'=>false,'response'=>['lat'=>$points[1],'lng'=>$points[0]]]);
        }else{
            return $this->asJson(['error'=>true]);
        }

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
                $reviews->isLimit = true;
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

	public function actionVseOtzyvy(int $review_id = null, int $photo_id = null)
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
                'initPhotoSliderParams' => [
                    'photoId' => $photo_id,
                    'reviewId' => $review_id,
                ]
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

    public function actionFeedback(){

        $this->enableCsrfValidation = true;
        Yii::$app->getRequest()->enableCsrfValidation = true;
        if ($this->enableCsrfValidation && Yii::$app->getErrorHandler()->exception === null &&
            !Yii::$app->getRequest()->validateCsrfToken())
        {
            throw new BadRequestHttpException(Yii::t('yii', 'Unable to verify your data submission.'));
        }

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

    public function actionError(){

        $currentUrl = Url::to();

        if($currentUrl === '/post/4392' || $currentUrl === '/post/1464'){
            $this->redirect('/21vekby-besplatnaa-dostavka-po-belarusi-n24');
        }else{
            return $this->render('404');
        }

    }

    public function actionAddComplain(){

        $response = new \stdClass();
        $response->success = true;
        $response->message = 'Спасибо, что помогаете!<br>Ваша жалоба будет рассмотрена модераторами';

        if (!Yii::$app->user->isGuest) {

            $id = Yii::$app->request->post('id', null);
            $message = Yii::$app->request->post('message', null);
            $type = Yii::$app->request->post('type', null);

            $complaint = new Complaints([
                'entities_id' => $id,
                'data' => $message,
                'user_id' => Yii::$app->user->id,
                'type' => $type,
                'status' => Complaints::$MODERATION_STATUS
            ]);

            if ($complaint->validate() && $complaint->save()) {
                return $this->asJson($response);
            } else {
                $nameAttribute = key($complaint->getErrors());
                $response->success = false;
                $response->message = $complaint->getFirstError($nameAttribute);
            }
            return $this->asJson($response);
        }

    }

    public function actionOtherPage(string $url_name){

        $page = OtherPage::find()
            ->where(['url_name'=>$url_name])
            ->one();


        if(!$page){

            throw new NotFoundHttpException('Cтраница не найдена');
        }

        return $this->render('otherPage',['page'=>$page]);

    }

    public function actionGetModalWindow(){

        return $this->renderPartial('modal_window.php');

    }

    public function actionAway(string $to)
    {
        return $this->redirect(urldecode($to));
    }

}
