<?php

namespace app\controllers;

use app\components\Helper;
use app\components\MainController;
use app\components\Pagination;
use app\models\entities\FavoritesPost;
use app\models\entities\Gallery;
use app\models\Posts;
use app\models\search\GallerySearch;
use app\models\uploads\UploadPostPhotos;
use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class PostController extends MainController
{

    public function actionIndex(int $id){

         $post = Posts::find()->with([
                'info',
                'workingHours'=>function ($query) {
                    $query->orderBy(['day_type'=>SORT_ASC]);
                },
                'city', 'totalView',
                'hasLike','categories.category'])
            ->where(['id'=>$id])
            ->one();
        $photoCount = Gallery::find()
            ->where(['post_id' => $id])
            ->count();
        $previewPhoto = Gallery::find()
            ->where(['post_id' => $id])
            ->orderBy(['user_status' => SORT_DESC, 'date' => SORT_DESC, 'link' => SORT_DESC])
            ->limit(4)->all();

        if($post){
            Helper::addViews($post->totalView);
            $breadcrumbParams = $this->getParamsForBreadcrumb($post);
            return $this->render('index', [
                'post'=>$post,
                'breadcrumbParams'=>$breadcrumbParams,
                'photoCount' => $photoCount,
                'previewPhoto' => $previewPhoto
            ]);
        }else{
            throw new NotFoundHttpException();
        }

    }

    public function getParamsForBreadcrumb($post){
        $breadcrumbParams=[];

        $currentUrl = Yii::$app->getRequest()->getHostInfo();
        $breadcrumbParams[] = [
            'name' => ucfirst(Yii::$app->getRequest()->serverName),
            'url_name' => $currentUrl,
            'pjax' => 'class="main-header-pjax a"'
        ];

        if($post->city){
            $currentUrl=$currentUrl.'/'.$post->city['url_name'];
            $breadcrumbParams[]=[
                'name'=>$post->city['name'],
                'url_name'=>$currentUrl,
                'pjax'=>'class="main-header-pjax a"'
            ];
        }

        if(isset($post->categories[0]['category'])){
            $currentUrl=$currentUrl.'/'.$post->categories[0]['category']['url_name'];
            $breadcrumbParams[]=[
                'name'=>$post->categories[0]['category']['name'],
                'url_name'=>$currentUrl,
                'pjax'=>'class="main-header-pjax a"'
            ];
        }

        if(isset($post->categories[0])){
            $currentUrl=$currentUrl.'/'.$post->categories[0]['url_name'];
            $breadcrumbParams[]=[
                'name'=>$post->categories[0]['name'],
                'url_name'=>$currentUrl,
                'pjax'=>'class="main-header-pjax a"'
            ];
        }

        $breadcrumbParams[]=[
            'name'=>$post['data'],
            'url_name'=>$post['url_name'].'-p'.$post['id'],
            'pjax'=>'class="main-pjax a"'
        ];

        return $breadcrumbParams;


    }

    public function actionFavoriteState()
    {
        $response = new \stdClass();
        $response->status='OK';
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;
        if( $request->isAjax && !Yii::$app->user->isGuest) {
            $itemId = (int)$request->post('itemId');

            $post = Posts::find()->select('count_favorites,id')->with('hasLike')->where(['id'=>$itemId])->one();

            if($post->hasLike){
                if($post->updateCounters(['count_favorites' => -1])){
                    if($post->hasLike->delete()){
                        $response->status='remove';
                    }
                }
            }else{
                if($post->updateCounters(['count_favorites' => 1])){
                    $model = new FavoritesPost([
                        'user_id'=>Yii::$app->user->id,
                        'post_id'=>$post->id
                    ]);
                    if($model->save()){
                        $response->status='add';
                    }
                }

            }
            $response->count=$post->count_favorites;

        }
        return $response;
    }

    public function actionUploadPhoto()
    {
        if(Yii::$app->user->isGuest) {
            throw new NotFoundHttpException('Cтраница не найдена');
        }

        if(Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $model = new UploadPostPhotos();
            $model->files = UploadedFile::getInstancesByName('post-photos');
            $model->postId = (int)Yii::$app->request->post('postId');
            if ($model->upload()) {
                return $this->asJson([
                    'success' => true,
                    'message' => 'Изменения сохранены',
                ]);
            } else {
                return $this->asJson([
                    'success' => false,
                    'message' => 'Изображение должно быть в формате JPG, GIF или PNG. Макс. размер файла: 15 МБ.'
                ]);
            }
        }
    }

    public function actionGetPhotos()
    {
        if (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $searchModel = new GallerySearch();
            $perPage = (int) $request->get('per-page', 16);
            $perPage = ($perPage < 16) ? 16 : $perPage;

            $pagination = new Pagination([
                'pageSize' => $perPage,
                'page' => $request->get('page', 1) - 1,
                'selfParams'=> [
                    'type' => true,
                    'postId' => true,
                ],
            ]);
            $loadTime = $request->get('loadTime', time());
            $dataProvider = $searchModel->search(
                $request->queryParams,
                $pagination,
                $loadTime
            );

            $response = new \stdClass();
            $response->data = $dataProvider->getModels();
            $response->url = $dataProvider->pagination->getLinks()['next'] ?? null;
            return $this->asJson($response);
        }
    }

    public function actionLoadMorePhotos()
    {
        if (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $searchModel = new GallerySearch();
            $pagination = new Pagination([
                'pageSize' => $request->get('per-page', 16),
                'page' => $request->get('page', 1) - 1,
                'selfParams'=> [
                    'type' => true,
                    'postId' => true,
                ],
            ]);
            $loadTime = $request->get('loadTime', time());
            $dataProvider = $searchModel->search(
                $request->queryParams,
                $pagination,
                $loadTime
            );
            return $this->renderPartial('photo-list', [
                'dataProvider' => $dataProvider,
                'loadTime' => $loadTime,
                'sequence' => (int)$request->get('sequence', 0)
            ]);
        }
    }

    public function actionGallery($postId)
    {
        $request = Yii::$app->request;
        $searchModel = new GallerySearch();
        $_GET['type'] = $request->get('type', 'user');
        $pagination = new Pagination([
            'pageSize' => $request->get('per-page', 3),
            'page' => $request->get('page', 1) - 1,
            'route' => Url::to(['post/load-more-photos']),
            'selfParams'=> [
                'type' => true,
                'postId' => true,
            ],
        ]);
        $loadTime = $request->get('loadTime', time());
        $dataProvider = $searchModel->search(
            $request->queryParams,
            $pagination,
            $loadTime
        );

        $post = Posts::find()->with([
            'info', 'workingHours',
            'city', 'totalView',
            'hasLike','categories.category'
        ])->where(['id' => $postId])
            ->one();
        $photoCount = Gallery::find()
            ->where(['post_id' => $postId])
            ->count();

        $breadcrumbParams = $this->getParamsForBreadcrumb($post);
        $breadcrumbParams[] = [
            'name' => 'Фотографии',
            'url_name' => $request->getUrl(),
            'pjax'=>'class="main-pjax a"'
        ];

        return $this->render('feed-photos.php', [
            'dataProvider' => $dataProvider,
            'ownerPhotos' => $searchModel->getAllOnwerPhotos(),
            'post' => $post,
            'breadcrumbParams' => $breadcrumbParams,
            'photoCount' => $photoCount,
            'loadTime' => $loadTime,
        ]);
    }
}
