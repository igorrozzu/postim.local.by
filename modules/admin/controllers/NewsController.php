<?php

namespace app\modules\admin\controllers;

use app\models\City;
use app\modules\admin\components\AdminDefaultController;
use app\modules\admin\models\News;
use yii\web\Controller;

/**
 * Default controller for the `admin` module
 */
class NewsController extends AdminDefaultController
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $news = new News();
        $cities = City::find()->select('id, name')
            ->orderBy('name')->asArray()->all();

        $params = [
            'news' => $news,
            'cities' => $cities,
        ];

        return $this->render('index', $params);
    }

    public function actionSave()
    {

        $news = new News();

        if (\Yii::$app->request->isPost && $news->load(\Yii::$app->request->post())) {
            if ($news->id) {
                $news = News::find()->where(['id' => $news->id])->one();
                $news->load(\Yii::$app->request->post());
            }
            if ($news->validate() && $news->save()) {
                $this->redirect('/' . $news->url_name . '-n' . $news->id);
            } else {
                $cities = City::find()->select('id, name')
                    ->orderBy('name')->asArray()->all();

                $toastMessage = [
                    'type' => 'error',
                    'message' => 'Произошла ошибка при отправке',
                ];

                $params = [
                    'news' => $news,
                    'cities' => $cities,
                    'toastMessage' => $toastMessage,
                ];

                return $this->render('edit_news', $params);
            }
        }

    }

    public function actionEditNews(int $id)
    {

        $news = News::find()->where(['id' => $id])->one();

        $cities = City::find()->select('id, name')
            ->orderBy('name')->asArray()->all();

        $params = [
            'news' => $news,
            'cities' => $cities,
        ];

        return $this->render('edit_news', $params);
    }
}
