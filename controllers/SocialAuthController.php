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
use app\models\User;
use Yii;
use yii\base\Exception;
use yii\helpers\Url;

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

    public function onAuthSuccess($client)
    {
        try {
            $attributes = $client->getUserAttributes();
            $socialAuthFactory = new SocialAuthAttributesFactory();
            $attrClient = $socialAuthFactory->getSocialAttributes($client->getId(), $attributes);

            $auth = SocialAuth::find()->where([
                'source' => $attrClient->getSocialName(),
                'source_id' => $attrClient->getSocialId(),
            ])->one();

            if (Yii::$app->user->isGuest) {
                if ($auth) {
                    $user = $auth->user;
                    Yii::$app->user->login($user, Yii::$app->params['user.loginDuration']);
                } else {
                    $email = $attrClient->getEmail();
                    if($email === null) {
                        $saveAttrKey = $attrClient->getSocialName() . $attrClient->getSocialId();
                        Yii::$app->cache->set($saveAttrKey, $attrClient, 1800);
                        return $this->redirect(['social-auth/set-required-fields', 'key' => $saveAttrKey]);
                    } else {

                        if ($user = User::findOne(['email' => $email])) {
                            if($user->isConfirmed()) {
                                Yii::$app->user->login($user, Yii::$app->params['user.loginDuration']);
                                $auth = new SocialAuth([
                                    'user_id' => Yii::$app->user->id,
                                    'source' => $attrClient->getSocialName(),
                                    'source_id' => $attrClient->getSocialId(),
                                    'screen_name' => $attrClient->getScreenName(),
                                ]);
                                $auth->save();
                            } else {
                                $newPassword = $user->generatePassword(Yii::$app->
                                params['user.socialAuthGeneratePasswordLength']);
                                //TODO create mail message
                                if($user->save()) {
                                    MailSender::sendSuccessRegisterThroughSocial($user, $newPassword);
                                }
                            }
                        } else {
                            $model = new SocialRegister();
                            $user = $model->createUser($attrClient);
                            if($user) {
                                Yii::$app->user->login($user, Yii::$app->params['user.loginDuration']);
                            }
                        }
                    }
                }
            } else { // Пользователь уже зарегистрирован
                if (!$auth) { // добавляем внешний сервис аутентификации
                    $auth = new SocialAuth([
                        'user_id' => Yii::$app->user->id,
                        'source' => $attrClient->getSocialName(),
                        'source_id' => $attrClient->getSocialId(),
                        'screen_name' => $attrClient->getScreenName(),
                    ]);
                    $auth->save();
                } else {
                    //TODO some account already has this binding
                }
                return $this->redirect(['user/settings']);
            }
        } catch (Exception $e){
            return $this->goHome();
        }
        return $this->goHome();
    }

    public function actionSetRequiredFields(string $key)
    {
        $model = new SocialRegister();
        $attrClient = Yii::$app->cache->get($key);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = $model->createUser($attrClient, true);
            if($user) {
                Yii::$app->user->login($user, Yii::$app->params['user.loginDuration']);
                return $this->goHome();
            }
        }
        return $this->render('required-fields-form', [
            'model' => $model,
            'name' => $model->name ?? $attrClient->getName()
        ]);
    }

}