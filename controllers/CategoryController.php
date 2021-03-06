<?php

namespace app\controllers;

use app\components\cardsPlaceWidget\CardsPlaceWidget;
use app\components\Helper;
use app\components\MainController;
use app\models\CategoryFeatures;
use app\models\Posts;
use app\models\PostsSearch;
use app\models\search\DiscountSearch;
use app\models\UnderCategoryFeatures;
use app\widgets\cardsDiscounts\CardsDiscounts;
use Yii;
use app\components\Pagination;
use yii\helpers\Json;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Request;

class CategoryController extends MainController
{

    public $category=false;
    public $under_category =false;

    static public $CATEGORY_PER_PAGE = 16;

    public function actionIndex()
    {
        $this->category = Yii::$app->request->get('category',false);
        $this->under_category =Yii::$app->request->get('under_category',false);

        $searchModel = new PostsSearch();

        $selfParams=['sort'=>true,'open'=>true,'filters'=>true];
        if(Yii::$app->request->isAjax && !Yii::$app->request->get('_pjax',false)){
            $selfFilterParams = Yii::$app->request->get('filters',false) ?
                $this->getSelfFilters($this->category,$this->under_category) : [];
        }else{
            $selfFilterParams = $this->getSelfFilters($this->category,$this->under_category)??[];
        }

        $selfParams = ArrayHelper::merge($selfParams,$selfFilterParams);

        $pagination = new Pagination([
            'pageSize' => Yii::$app->request->get('per-page', static::$CATEGORY_PER_PAGE),
            'page' => Yii::$app->request->get('page', 1)-1,
            'route'=>Yii::$app->request->getPathInfo(),
            'selfParams'=>$selfParams
        ]);

        $paramSort = Yii::$app->request->get('sort', '_rating');
        $sort = PostsSearch::getSortArray($paramSort);

        $loadTime = Yii::$app->request->get('loadTime', time());
        $geolocation = Yii::$app->request->cookies->getValue('geolocation') ?
            Json::decode(Yii::$app->request->cookies->getValue('geolocation')) : null;
        $loadGeolocation = Yii::$app->request->get('load-geolocation', $geolocation);
        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            $pagination,
            $sort,
            $loadTime,
            $selfFilterParams,
            $loadGeolocation
        );

        $breadcrumbParams = $this->getParamsForBreadcrumb();

        $discountSearch = new DiscountSearch();
        $discountCount = $discountSearch->getCountByCityAndCategory(Yii::$app->request->queryParams);

        $params=[
            'dataProvider'=>$dataProvider,
            'sort'=>$paramSort,
            'selfParams'=> $selfParams,
            'breadcrumbParams'=>$breadcrumbParams,
            'loadTime' => $loadTime,
            'loadGeolocation'=>$loadGeolocation,
            'keyForMap'=>$searchModel->getKeyForPlacesOnMap(),
            'issetFilters' => !!$selfFilterParams,
            'discountCount' => $discountCount,
        ];

        if(Yii::$app->request->isAjax && !Yii::$app->request->get('_pjax',false) ){
            echo CardsPlaceWidget::widget([
                'dataprovider' => $dataProvider,
                'settings' => [
                    'show-more-btn' => true,
                    'replace-container-id' => 'feed-posts',
                    'load-time' => $loadTime,
                    'load-geolocation'=>$loadGeolocation
                ]
            ]);
        }else{
            return $this->render('index',$params);
        }

    }

    private function getParamsForBreadcrumb(){
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

        $breadcrumbParams[]=[
            'name'=>$this->category['name'],
            'url_name'=>$currentUrl.'/'.$this->category['url_name'],
            'pjax'=>'class="main-pjax a"'
        ];

        if($this->under_category){
            $breadcrumbParams[]=[
                'name'=>$this->under_category['name'],
                'url_name'=>$currentUrl.'/'.$this->under_category['url_name'],
                'pjax'=>'class="main-pjax a"'
            ];
        }


        return $breadcrumbParams;
    }

    public function actionGetFilters(){
        $arrayRequest= explode('/',Yii::$app->request->post('url',''));
        $category = array_pop($arrayRequest);
        $under_category = false;
        $features=[];

        if($cat=Yii::$app->category->getUnderCategoryByName($category)){
            $features= UnderCategoryFeatures::find()
                ->innerJoinWith('features')
                ->where(['under_category_id'=>$cat->id])
                ->andWhere(['filter_status'=>1])
                ->andWhere(['main_features'=>null])
                ->all();
            $under_category = $category;
            $category=false;
        }
        if($cat=Yii::$app->category->getCategoryByName($category)){
            $features= CategoryFeatures::find()
                ->joinWith('features')
                ->where(['category_id'=>$cat->id])
                ->andWhere(['filter_status'=>1])
                ->andWhere(['main_features'=>null])
                ->all();
            $under_category = false;
        }

        $response = new \stdClass();
        $response->rubrics=[];
        $response->additionally=[];

        foreach ($features as $feature){
            if($feature->features->type==1){
                array_push($response->additionally,$feature->features);
            }else{
                if($feature->features->type==2){
                    $feature->features->setMinMax($category,$under_category);
                    array_unshift($response->rubrics,$feature->features);
                }else{
                    array_push($response->rubrics,$feature->features);
                }

            }
        }

        return $this->renderPartial('_filters',['features'=>$response]);
    }

    private function getSelfFilters($category,$under_category){

        $filtersSelf = [];

        if($under_category){
            $features= UnderCategoryFeatures::find()
                ->innerJoinWith('features')
                ->where(['under_category_id'=>$under_category->id])
                ->andWhere(['filter_status'=>1])
                ->andWhere(['main_features'=>null])
                ->all();

            foreach ($features as $feature){
                if($feature->features->underFeatures==null){
                    $filtersSelf[$feature->features->id]=true;
                }else{
                    foreach ($feature->features->underFeatures as $underFeature){
                        $filtersSelf[$underFeature->id]=true;
                    }
                }
            }

        }
        if($category){

            $features= CategoryFeatures::find()
                ->innerJoinWith('features')
                ->where(['category_id'=>$category->id])
                ->andWhere(['filter_status'=>1])
                ->andWhere(['main_features'=>null])
                ->all();

            foreach ($features as $feature){
                if($feature->features->underFeatures==null){
                    $filtersSelf[$feature->features->id]=true;
                }else{
                    foreach ($feature->features->underFeatures as $underFeature){
                        $filtersSelf[$underFeature->id]=true;
                    }
                }
            }

        }

        return $filtersSelf;
    }

    public function actionGetDiscounts()
    {
        $this->category = Yii::$app->request->get('category',false);
        $this->under_category =Yii::$app->request->get('under_category',false);

        $request = Yii::$app->request;
        $discountSearch = new DiscountSearch();
        $pagination = new Pagination([
            'pageSize' => $request->get('per-page', 8),
            'page' => $request->get('page', 1) - 1,
            'route' => Yii::$app->request->getPathInfo(),
            'selfParams' => [
                'city' => true,
                'category' => true,
                'open' => true,
                'sort' => true,
            ],
        ]);

        $loadTime = Yii::$app->request->get('loadTime', time());
        $geoLocation = Yii::$app->request->cookies->getValue('geolocation') ?
            Json::decode(Yii::$app->request->cookies->getValue('geolocation')) : null;
        $dataProvider = $discountSearch->searchByCityAndCategory(
            $request->queryParams,
            $pagination,
            $loadTime,
            $geoLocation
        );

        $breadcrumbParams = $this->getParamsForBreadcrumb();
        $breadcrumbParams[] = [
            'name' => 'Скидки',
            'url_name' => '/' . Yii::$app->getRequest()->getPathInfo(),
            'pjax' => 'class="main-pjax a"'
        ];

        $selfParams = ['sort' => true, 'open' => true];

        if (Yii::$app->request->isAjax && !Yii::$app->request->get('_pjax',false) ) {
            return CardsDiscounts::widget([
                'dataprovider' => $dataProvider,
                'settings' => [
                    'show-more-btn' => true,
                    'replace-container-id' => 'feed-discounts',
                    'load-time' => $loadTime,
                    'postId' => false,
                    'show-distance' => true,
                ]
            ]);
        } else {

            $postSearch = new PostsSearch();
            $postCount = $postSearch->getCountByCityAndCategory(Yii::$app->request->queryParams);

            return $this->render('feed-discounts', [
                'dataProvider' => $dataProvider,
                'breadcrumbParams' => $breadcrumbParams,
                'loadTime' => $loadTime,
                'selfParams' => $selfParams,
                'keyForMap' => $discountSearch->getKey(),
                'postCount' => $postCount,
                'urlPost' => str_replace('/skidki', '', Yii::$app->request->getPathInfo()),
                'sort' => Yii::$app->request->get('sort', 'new')
            ]);
        }
    }
}
