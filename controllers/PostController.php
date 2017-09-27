<?php

namespace app\controllers;

use app\components\Helper;
use app\components\MainController;
use app\components\Pagination;
use app\models\AddPost;
use app\models\City;
use app\models\entities\FavoritesPost;
use app\models\entities\Gallery;
use app\models\entities\GalleryComplaint;
use app\models\moderation_post\PostsModeration;
use app\models\Posts;
use app\models\search\GallerySearch;
use app\models\UnderCategory;
use app\models\UnderCategoryFeatures;
use app\models\uploads\UploadPostPhotos;
use app\models\uploads\UploadPostPhotosTmp;
use linslin\yii2\curl\Curl;
use Yii;
use yii\db\Exception;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class PostController extends MainController
{

    public function actionIndex(int $id, string $photo_id = null){

         $post = Posts::find()->with([
                'info',
                'workingHours'=>function ($query) {
                    $query->orderBy(['day_type'=>SORT_ASC]);
                },
                'city', 'totalView',
                'hasLike','onlyOnceCategories.category'])
            ->where(['id'=>$id])
            ->one();

        if($post){
            Helper::addViews($post->totalView);
            $breadcrumbParams = $this->getParamsForBreadcrumb($post);

            $queryPost =  Posts::find()->where(['tbl_posts.id'=>$id])
                ->prepare(Yii::$app->db->queryBuilder)
                ->createCommand()->rawSql;
            $keyForMap = Helper::saveQueryForMap($queryPost);

            return $this->render('index', [
                'post'=>$post,
                'breadcrumbParams'=>$breadcrumbParams,
                'photoCount' => Gallery::getPostPhotoCount($id),
                'previewPhoto' => Gallery::getPreviewPostPhoto($id, 4),
                'photoId' => $photo_id,
                'keyForMap'=>$keyForMap
            ]);
        }else{
            throw new NotFoundHttpException();
        }

    }

    public function actionPostModeration($id){
		$post = PostsModeration::find()->with([
			'info',
			'workingHours'=>function ($query) {
				$query->orderBy(['day_type'=>SORT_ASC]);
			},
			])
			->where(['id'=>$id])
			->one();

		if($post){
			Helper::addViews($post->totalView);

			$queryPost =  PostsModeration::find()->where(['tbl_posts_moderation.id'=>$id])
				->prepare(Yii::$app->db->queryBuilder)
				->createCommand()->rawSql;
			$keyForMap = Helper::saveQueryForMap($queryPost);

			return $this->render('index_moderation', [
				'post'=>$post,
				'keyForMap'=>$keyForMap
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
            $currentUrl=$currentUrl.$post->city['url_name']?'/'.$post->city['url_name']:'';
            $breadcrumbParams[]=[
                'name'=>$post->city['name'],
                'url_name'=>$currentUrl,
                'pjax'=>'class="main-header-pjax a"'
            ];
        }

        if(isset($post->onlyOnceCategories[0]['category'])){
            $breadcrumbParams[]=[
                'name'=>$post->onlyOnceCategories[0]['category']['name'],
                'url_name'=>$currentUrl.'/'.$post->onlyOnceCategories[0]['category']['url_name'],
                'pjax'=>'class="main-header-pjax a"'
            ];
        }

        if(isset($post->onlyOnceCategories[0])){
            $currentUrl=$currentUrl.'/'.$post->onlyOnceCategories[0]['url_name'];
            $breadcrumbParams[]=[
                'name'=>$post->onlyOnceCategories[0]['name'],
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
                    'message' => 'Изображение должно быть в формате JPG, GIF или PNG. Макс. размер файла: 15 МБ. Не более 10 файлов'
                ]);
            }
        }
    }

    public function actionUploadTmpPhoto(){
		if(Yii::$app->user->isGuest) {
			throw new NotFoundHttpException('Cтраница не найдена');
		}

		if(Yii::$app->request->isAjax && Yii::$app->request->isPost) {
			$model = new UploadPostPhotosTmp();
			$model->files = UploadedFile::getInstancesByName('photos');
			if ($model->upload()) {
				return $this->asJson(['success' => true,'data'=>$model->getSavedFiles()]);
			} else {
				return $this->asJson([
					'success' => false,
					'message' => 'Изображение должно быть в формате JPG, GIF или PNG. Макс. размер файла: 15 МБ. Не более 10 файлов'
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
                    'photo_id' => true,
                ],
            ]);
            $loadTime = $request->get('loadTime', time());
            $dataProvider = $searchModel->search(
                $request->queryParams,
                $pagination,
                $loadTime
            );
            $response = new \stdClass();

            if (isset($request->queryParams['photo_id'])) {
                $count = $searchModel->getPreviewsPhotoCount($loadTime);
                $page = (int) ($count / 16);
                $dataProvider->pagination->pageSize = ($page === 0) ? 16 : ($page + 1) * 16;
                $response->data = $dataProvider->getModels();
                $dataProvider->pagination->page = $page;
                $dataProvider->pagination->pageSize = 16;
                $response->url = $dataProvider->pagination->getLinks()['next'] ?? null;
                $response->sequence = $count - 1;
            } else {
                $response->data = $dataProvider->getModels();
                $response->url = $dataProvider->pagination->getLinks()['next'] ?? null;
            }
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

    public function actionGallery(int $postId, string $photo_id = null)
    {
        $request = Yii::$app->request;
        $searchModel = new GallerySearch();
        $_GET['type'] = $request->get('type', 'user');
        $pagination = new Pagination([
            'pageSize' => $request->get('per-page', 16),
            'page' => $request->get('page', 1) - 1,
            'route' => Url::to(['post/load-more-photos']),
            'selfParams'=> [
                'type' => true,
                'postId' => true,
                'photo_id' => true,
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

        $queryPost =  Posts::find()->where(['tbl_posts.id'=>$postId])
            ->prepare(Yii::$app->db->queryBuilder)
            ->createCommand()->rawSql;
        $keyForMap = Helper::saveQueryForMap($queryPost);

        return $this->render('feed-photos.php', [
            'dataProvider' => $dataProvider,
            'ownerPhotos' => $searchModel->getAllOnwerPhotos(),
            'post' => $post,
            'breadcrumbParams' => $breadcrumbParams,
            'photoCount' => $photoCount,
            'loadTime' => $loadTime,
            'keyForMap'=>$keyForMap,
            'photoId' => $photo_id
        ]);
    }

    public function actionComplainGallery(){
        $response = new \stdClass();
        $response->success = true;
        $response->message = 'Спасибо, что помогаете!<br>Ваша жалоба будет рассмотрена модераторами';

        if (!Yii::$app->user->isGuest) {
            $photoId = Yii::$app->request->post('id', null);
            $message = Yii::$app->request->post('message', null);
            $galleryComplaint = new GalleryComplaint([
                'photo_id' => $photoId,
                'message' => $message,
                'user_id' => Yii::$app->user->id
            ]);

            if ($galleryComplaint->validate() && $galleryComplaint->save()) {
                return $this->asJson($response);
            } else {
                $nameAttribute = key($galleryComplaint->getErrors());
                $response->success = false;
                $response->message = $galleryComplaint->getFirstError($nameAttribute);
            }
            return $this->asJson($response);
        }
    }

    public function actionGetPlaceForMap(string $id){
        $response = new \stdClass();
        $response->status='error';
        Yii::$app->response->format = Response::FORMAT_JSON;

        $queryFromRepository = Yii::$app->cache->get($id);
        if($queryFromRepository){
            $query = unserialize($queryFromRepository);
            $response->places = Yii::$app->db->createCommand($query)->queryAll();
            foreach ($response->places as &$place){
                if($place['coordinates']){
                    $latLng = explode(',',$place['coordinates']);
                    $place['lat'] = str_replace('(','',$latLng[0]);
                    $place['lon'] = str_replace(')','',$latLng[1]);
                    unset($place['coordinates']);
                }
            }
            $response->status = 'OK';
        }
        return $response;
    }

    public function actionGetPopupPlaceForMap(int $id){
        $post = Posts::find()
            ->with('hasLike')
            ->with(['workingHours'=>function ($query) {
                $query->orderBy(['day_type'=>SORT_ASC]);
            }])
            ->where(['tbl_posts.id'=>$id])
            ->one();
        return $this->renderAjax('__popup_place',['post'=>$post]);
    }


    public function actionAdd(){

    	if(!Yii::$app->user->isGuest){
			$categories = UnderCategory::find()->select('id , name')->orderBy('name')->asArray()->all();
			$cities = City::find()->select('id, name')->orderBy('name')->asArray()->all();
			$params = ['categories' => $categories,
				'cities'     => $cities,
			];
			return $this->render('add', ['params' => $params]);
		}else{
			throw new NotFoundHttpException();
		}

    }

    public function actionEdit(int $id,$moderation = null){

    	if(!Yii::$app->user->isGuest){
			$categories = UnderCategory::find()->select('id , name')->orderBy('name')->asArray()->all();
			$cities = City::find()->select('id, name')->orderBy('name')->asArray()->all();
			$photos = [];
			$post = null;

			if($moderation === null){
				$photos = Gallery::find()->where(['post_id' => $id, 'user_status' => 1])->all();
				if($photos==null){
					$photos = [];
				}
				$post = Posts::find()->where(['id'=>$id])->with(['city', 'info','workingHours' => function ($query) {
							$query->orderBy(['day_type' => SORT_ASC]);
						}])->one();
			}else{
				$post = PostsModeration::find()->where(['id'=>$id])->with(['city', 'info','workingHours' => function ($query) {
					$query->orderBy(['day_type' => SORT_ASC]);
				}])->one();
			}


			$params = ['categories' => $categories,
				'cities' => $cities,
				'post' => $post,
				'photos' => $photos,
				'moderation' => $moderation
			];

			return $this->render('edit', ['params' => $params]);
		}else{
			throw new NotFoundHttpException();
		}
	}

    public function actionSavePost(){
		if(!Yii::$app->user->isGuest && Yii::$app->request->isPost){
			$addPostModel = new AddPost();
			$addPostModel->setScenario(Yii::$app->user->identity->role > 1 ? AddPost::$SCENARIO_ADD_MODERATOR : AddPost::$SCENARIO_ADD_USER);
			if($addPostModel->load(Yii::$app->request->post(),'') && $addPostModel->save()){
				return $this->redirect('/id'.Yii::$app->user->getId());
			}
		}
	}

	public function actionSaveEditPost(){
		if(!Yii::$app->user->isGuest && Yii::$app->request->isPost){
			$addPostModel = new AddPost();
			$addPostModel->setScenario(Yii::$app->user->identity->role > 1 ? AddPost::$SCENARIO_EDIT_MODERATOR : AddPost::$SCENARIO_EDIT_USER);
			if($addPostModel->load(Yii::$app->request->post(),'') && $addPostModel->save()){
				return $this->redirect('/id'.Yii::$app->user->getId());
			}
		}
	}

    public function actionGetFeaturesByCategories(){
		Yii::$app->response->format = Response::FORMAT_JSON;
    	$ids = Yii::$app->request->get('categories');

		$features = UnderCategoryFeatures::find()->select('features_id')->distinct('features_id')
			->innerJoinWith('features')
			->where(['under_category_id'=>$ids])
			->andWhere(['main_features'=>null])
			->all();

		$response = new \stdClass();
		$response->rubrics=[];
		$response->additionally=[];

		foreach ($features as $feature){
			if($feature->features->type==1){
				array_push($response->additionally,$feature->features);
			}else{
				if($feature->features->type==2){
					array_unshift($response->rubrics,$feature->features);
				}else{
					$featureStd = new \stdClass();
					$featureStd->underFeatures = $feature->features->underFeatures;
					$featureStd->id = $feature->features->id;
					$featureStd->name = $feature->features->name;
					$featureStd->type = $feature->features->type;
					$featureStd->filter_status = $feature->features->filter_status;
					$featureStd->main_features = $feature->features->main_features;
					array_push($response->rubrics,$featureStd);
				}

			}
		}

		return $response;


	}

	public function actionGetCodeVideo(){
		$query = Yii::$app->request->get('query');

		$response = new \stdClass();

		if ($query) {
			$curl = new Curl();
			$response = $curl->get($query);
		}
		return $response;
	}

	public function actionGetPhotoInfo(){
		$params = [
			'src'=> Yii::$app->request->get('src',''),
			'description'=> Yii::$app->request->get('description','')
		];

		return $this->renderAjax('__form_photo_info',$params);
	}
}
