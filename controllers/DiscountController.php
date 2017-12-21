<?php

namespace app\controllers;

use app\components\Helper;
use app\components\MainController;
use app\components\Pagination;
use app\models\Discounts;
use app\models\entities\Gallery;
use app\models\Posts;
use app\models\search\DiscountSearch;
use app\models\uploads\UploadPhotos;
use app\models\uploads\UploadPhotosByUrl;
use app\models\uploads\UploadPostPhotosTmp;
use app\widgets\cardsDiscounts\CardsDiscounts;
use Yii;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class DiscountController extends MainController
{
    public function actionAdd(int $postId)
    {
        $model = new Discounts([
            'date_start' => time(),
            'status' => Discounts::STATUS['active'],
            'post_id' => $postId,
            'cover' => 'default.png',
        ]);

        if (Yii::$app->request->isPost && !Yii::$app->user->isGuest) {

            $model->load(Yii::$app->request->post(), 'discount');
            $model->conditions = Json::encode( $model->conditions );
            $model->photos = Yii::$app->request->post('photos');

            if ($model->create()) {
                $post = Posts::findOne($postId);
                return $this->redirect(Url::to([
                    'post/get-discounts-by-post',
                    'name' => $post->url_name,
                    'postId' => $postId,
                ]));
            }
        }

        return $this->render('add', [
            'model' => $model,
            'errors' => array_values($model->getFirstErrors()),
        ]);
    }

    public function actionUploadTmpPhoto()
    {
        if (Yii::$app->user->isGuest) {
            throw new NotFoundHttpException('Cтраница не найдена');
        }

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $model = new UploadPostPhotosTmp();
            $model->setDirectory("@webroot/discount_photo/tmp/");

            $model->files = UploadedFile::getInstancesByName('photos');
            if ($model->upload()) {
                return $this->asJson([
                    'success' => true,
                    'data' => $model->getSavedFiles(),
                    'folder' => 'discount_photo',
                ]);
            } else {
                return $this->asJson([
                    'success' => false,
                    'message' => 'Изображение должно быть в формате JPG, GIF или PNG. Макс. размер файла: 15 МБ. Не более 10 файлов'
                ]);
            }
        }
    }

    public function actionUploadNewPhoto(int $postId)
    {
        if (Yii::$app->user->isGuest) {
            throw new NotFoundHttpException('Cтраница не найдена');
        }

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $model = new UploadPhotos();
            $model->directory = '/discount-img/' . $postId . '/';
            $model->files = UploadedFile::getInstancesByName('photos');
            if ($model->upload()) {
                return $this->asJson(['success' => true, 'data' => $model->getSavedFiles()]);
            } else {
                return $this->asJson([
                    'success' => false,
                    'message' => 'Изображение должно быть в формате JPG, GIF или PNG. Макс. размер файла: 15 МБ. Не более 10 файлов'
                ]);
            }
        }
    }

    public function actionUploadNewPhotoByUrl(int $postId)
    {
        if (Yii::$app->user->isGuest) {
            throw new NotFoundHttpException('Cтраница не найдена');
        }

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $model = new UploadPhotosByUrl();
            $model->directory = '/discount-img/' . $postId . '/';
            $model->urlToImg = Yii::$app->request->post('url');
            if ($model->upload()) {
                return $this->asJson(['success' => true, 'data' => $model->getSavedFiles()]);
            } else {
                return $this->asJson([
                    'success' => false,
                    'message' => 'Изображение должно быть в формате JPG, GIF или PNG. Макс. размер файла: 15 МБ. Не более 10 файлов'
                ]);
            }
        }
    }

    public function actionLoadMoreDiscounts(int $postId)
    {
        $request = Yii::$app->request;
        $model = new DiscountSearch();
        $pagination = new Pagination([
            'pageSize' => $request->get('per-page', 1),
            'page' => $request->get('page', 1) - 1,
        ]);
        $loadTime = $request->get('loadTime', time());

        $dataProvider = $model->searchByPost(
            $request->queryParams,
            $pagination,
            $loadTime
        );

        if ($request->isAjax && !$request->get('_pjax',false) ){
            return CardsDiscounts::widget([
                'dataProvider' => $dataProvider,
                'settings'=>[
                    'show-more-btn' => true,
                    'replace-container-id' => 'feed-discount',
                    'loadTime' => $loadTime,
                    'postId' => $postId,
                    'show-distance' => false,
                ]
            ]);
        }
    }

    public function actionRead(int $discountId)
    {
        $discount = Discounts::find()
            ->innerJoinWith(['post', 'totalView'])
            ->where([Discounts::tableName() . '.id' => $discountId])
            ->one();

        $breadcrumbParams = $this->getParamsForBreadcrumb($discount->post);
        $breadcrumbParams[] = [
            'name' => 'Скидки',
            'url_name' => Url::to([
                'post/get-discounts-by-post',
                'name' => $discount->post->url_name,
                'postId' => $discount->post->id,
            ]),
            'pjax' => 'class="main-pjax a"'
        ];
        $breadcrumbParams[] = [
            'name' => $discount->header,
            'url_name' => Yii::$app->request->getUrl(),
            'pjax' => 'class="main-pjax a"'
        ];

        Helper::addViews($discount->totalView);

        $queryPost = Posts::find()
            ->where([Posts::tableName() . '.id' => $discount->post->id])
            ->prepare(Yii::$app->db->queryBuilder)
            ->createCommand()->rawSql;
        $keyForMap = Helper::saveQueryForMap($queryPost);

        $photoCount = Gallery::find()
            ->where(['post_id' => $discount->post->id])
            ->count();
        $discountCount = Discounts::find()
            ->where(['post_id' => $discount->post->id])
            ->count();

        $economy = $discount->price ?
            round($discount->price * $discount->discount / 100, 2) : null;

        return $this->render('index', [
            'discount' => $discount,
            'post' => $discount->post,
            'breadcrumbParams' => $breadcrumbParams,
            'keyForMap' => $keyForMap,
            'photoCount' => $photoCount,
            'discountCount' => $discountCount,
            'economy' => $economy
        ]);
    }

    public function getParamsForBreadcrumb($post)
    {
        $breadcrumbParams = [];

        $currentUrl = Yii::$app->getRequest()->getHostInfo();
        $breadcrumbParams[] = [
            'name' => ucfirst(Yii::$app->getRequest()->serverName),
            'url_name' => $currentUrl,
            'pjax' => 'class="main-header-pjax a"'
        ];

        if ($post->city) {
            $currentUrl = $currentUrl . $post->city['url_name'] ? '/' . $post->city['url_name'] : '';
            $breadcrumbParams[] = [
                'name' => $post->city['name'],
                'url_name' => $currentUrl,
                'pjax' => 'class="main-header-pjax a"'
            ];
        }

        if (isset($post->onlyOnceCategories[0]['category'])) {
            $breadcrumbParams[] = [
                'name' => $post->onlyOnceCategories[0]['category']['name'],
                'url_name' => $currentUrl . '/' . $post->onlyOnceCategories[0]['category']['url_name'],
                'pjax' => 'class="main-header-pjax a"'
            ];
        }

        if (isset($post->onlyOnceCategories[0])) {
            $currentUrl = $currentUrl . '/' . $post->onlyOnceCategories[0]['url_name'];
            $breadcrumbParams[] = [
                'name' => $post->onlyOnceCategories[0]['name'],
                'url_name' => $currentUrl,
                'pjax' => 'class="main-header-pjax a"'
            ];
        }

        $breadcrumbParams[] = [
            'name' => $post['data'],
            'url_name' => $post['url_name'] . '-p' . $post['id'],
            'pjax' => 'class="main-pjax a"'
        ];
        $breadcrumbParams[] = [
            'name' => 'Скидки',
            'url_name' => $post['url_name'] . '-p' . $post['id'],
            'pjax' => 'class="main-pjax a"'
        ];

        return $breadcrumbParams;
    }

    public function actionOrder()
    {
        return $this->render('order');
    }

    public function actionMegamoney()
    {
        return $this->render('basket-lack-of-mega-money');
    }

    public function actionMoney()
    {
        return $this->render('basket-lack-of-money');
    }
}