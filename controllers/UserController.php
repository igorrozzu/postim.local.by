<?php

namespace app\controllers;

use app\components\MainController;
use app\models\uploads\UploadUserPhoto;
use Yii;
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

        if(Yii::$app->request->isPost) {

            return $this->render('settings-form');
        }

        $socialBindings = Yii::$app->user->identity->socialBindings;
        return $this->render('settings-form', [
            'socialBindings' => $socialBindings
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
}
