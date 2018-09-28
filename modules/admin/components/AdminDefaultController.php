<?php

namespace app\modules\admin\components;

use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;

/**
 * Default controller for the `admin` module
 */
class AdminDefaultController extends Controller
{

    public function init()
    {
        parent::init();

        if (!\Yii::$app->user->isModerator()) {
            throw new NotFoundHttpException('Cтраница не найдена');
        }

        $secretCookies = \Yii::$app->request->cookies->getValue('tokenWewefwf');

        if (\Yii::$app->user->generationSecretToken() === $secretCookies) {

        } elseIf ($tokenWewefwf = \Yii::$app->request->get('tokenWewefwf', false)) {

            $currentUrl = \Yii::$app->request->getPathInfo();
            if ($currentUrl != 'admin/default/set-wewefwf') {

                throw new NotFoundHttpException('Cтраница не найдена');
            }

        } else {
            throw new NotFoundHttpException('Cтраница не найдена');
        }

        $this->layout = 'main';

    }
}
