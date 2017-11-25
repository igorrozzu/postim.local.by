<?php

namespace app\modules\admin\controllers;

use app\components\Pagination;
use app\modules\admin\components\AdminDefaultController;
use app\modules\admin\models\Category;
use app\modules\admin\models\CategorySearch;
use app\modules\admin\models\News;
use app\modules\admin\models\OtherPage;
use app\modules\admin\models\OtherPageSearch;
use app\modules\admin\models\post\Posts;
use app\modules\admin\models\post\PostsSearch;
use app\modules\admin\models\RightBaner;
use app\modules\admin\models\UnderCategory;
use app\modules\admin\models\UnderCategorySearch;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

/**
 * Default controller for the `admin` module
 */
class PostController extends AdminDefaultController
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {

    }

    public function actionCategories(){

        $model = new UnderCategory();
        $categories =  Category::find()
            ->select(['id','name'])
            ->orderBy(['name'=>SORT_ASC])
            ->asArray(true)->all();

        $modelCategory = new Category();


        return $this->render('categories',[
            'model'=>$model,
            'categories'=>$categories,
            'modelCategory'=>$modelCategory
        ]);

    }

    public function actionDeleteCategories(){

        $searchModel = new CategorySearch();

        $pagination = new Pagination([
            'pageSize' => \Yii::$app->request->get('per-page', 8),
            'page' => \Yii::$app->request->get('page', 1)-1,
            'route'=>'/admin/post/delete-categories',
        ]);

        $dataProvider = $searchModel->search(\Yii::$app->request->get(),$pagination);


        $searchModelUnder = new UnderCategorySearch();
        $paginationUnder = new Pagination([
            'pageSize' => \Yii::$app->request->get('per-page', 8),
            'page' => \Yii::$app->request->get('page', 1)-1,
            'route'=>'/admin/post/delete-categories',
        ]);

        $dataProviderUnder = $searchModelUnder->search(\Yii::$app->request->get(),$paginationUnder);


        return  $this->render('deleteCategory',['dataProvider'=>$dataProvider,
            'searchModel'=>$searchModel,
            'dataProviderUnder'=>$dataProviderUnder,
            'searchModelUnder'=>$searchModelUnder,
        ]);

    }

    public function actionActDeleteCategories(){

        $act = \Yii::$app->request->get('act',false);
        $id = \Yii::$app->request->get('id');

        try{
            if($act && $id){
                if($act == 'category'){
                    $category = Category::find()->where(['id'=>$id])->one();
                    if($category){
                        $category->delete();
                    }
                }elseif($act == 'under_category'){
                    $underCategory = UnderCategory::find()->where(['id'=>$id])->one();
                    if($underCategory){
                        $underCategory->delete();
                    }
                }
            }
        }catch (Exception $e){

        }


        $searchModel = new CategorySearch();

        $pagination = new Pagination([
            'pageSize' => \Yii::$app->request->get('per-page', 8),
            'page' => \Yii::$app->request->get('page', 1)-1,
            'route'=>'/admin/post/delete-categories',
        ]);

        $dataProvider = $searchModel->search(\Yii::$app->request->get(),$pagination);

        $searchModelUnder = new UnderCategorySearch();
        $paginationUnder = new Pagination([
            'pageSize' => \Yii::$app->request->get('per-page', 8),
            'page' => \Yii::$app->request->get('page', 1)-1,
            'route'=>'/admin/post/delete-categories',
        ]);

        $dataProviderUnder = $searchModelUnder->search(\Yii::$app->request->get(),$paginationUnder);


        return  $this->render('deleteCategory',['dataProvider'=>$dataProvider,
            'searchModel'=>$searchModel,
            'dataProviderUnder'=>$dataProviderUnder,
            'searchModelUnder'=>$searchModelUnder,
        ]);

    }

    public function actionUnderCategorySave(){
        $model = new UnderCategory();
        $categories =  Category::find()
            ->select(['id','name'])
            ->orderBy(['name'=>SORT_ASC])
            ->asArray(true)->all();

        $modelCategory = new Category();

        $toastMessage = [
            'type' => 'error',
            'message' => 'Произошла ошибка',
        ];

        if(\Yii::$app->request->isPost){
            if($model->load(\Yii::$app->request->post())){
                if($model->save()){
                    $model = new UnderCategory();
                    $toastMessage = [
                        'type' => 'success',
                        'message' => 'Категория была добавлена',
                    ];
                }
            }
        }


        return $this->render('categories',[
            'model'=>$model,
            'categories'=>$categories,
            'modelCategory'=>$modelCategory,
            'toastMessage' => $toastMessage
        ]);

    }

    public function actionCategorySave(){

        $modelCategory = new Category();

        $toastMessage = [
            'type' => 'error',
            'message' => 'Произошла ошибка',
        ];

        if(\Yii::$app->request->isPost){
            if($modelCategory->load(\Yii::$app->request->post())){
                if($modelCategory->save()){
                    $modelCategory = new UnderCategory();

                    $toastMessage = [
                        'type' => 'success',
                        'message' => 'Категория была добавлена',
                    ];
                }
            }
        }

        $model = new UnderCategory();
        $categories =  Category::find()
            ->select(['id','name'])
            ->orderBy(['name'=>SORT_ASC])
            ->asArray(true)->all();


        return $this->render('categories',[
            'model'=>$model,
            'categories'=>$categories,
            'modelCategory'=>$modelCategory,
            'toastMessage' => $toastMessage
        ]);

    }

    public function actionOtherPage(){

        $editPage = new OtherPage();
        $editPage->setScenario(OtherPage::$FIND_PAGE);
        if(\Yii::$app->request->isPost){
            if($editPage->load(\Yii::$app->request->post()) && $editPage->validate()){

                if($findPage = $editPage->getData()){
                    $findPage->find_url = $editPage->find_url;
                    $editPage = $findPage;
                }

                return $this->render('otherPageSave',['editPage'=>$editPage]);
            }else{

                $toastMessage = [
                    'type' => 'error',
                    'message' => 'Произошла ошибка',
                ];

                return $this->render('otherPage',['editPage'=>$editPage,'toastMessage'=>$toastMessage]);
            }

        }else{
            return $this->render('otherPage',['editPage'=>$editPage]);
        }
    }

    public function actionOtherPageSave(){
        $editPage = new OtherPage();
        $editPage->setScenario(OtherPage::$ADD_PAGE);

        if(\Yii::$app->request->isPost){

            if($editPage->load(\Yii::$app->request->post())){

                if($editPage->validate()){
                    $editPage->save();
                }else{
                    $editPage = $editPage->getData();
                    $editPage->setScenario(OtherPage::$EDIT_PAGE);
                    $editPage->load(\Yii::$app->request->post());
                    $editPage->update();
                }

                $editPage = new OtherPage();

                $toastMessage = [
                    'type' => 'success',
                    'message' => 'Страница сохранена',
                ];

                return $this->render('otherPage',['editPage'=>$editPage,'toastMessage'=>$toastMessage]);
            }


        }else{
            return $this->render('otherPage',['editPage'=>$editPage]);
        }
    }

    public function actionOtherPageDelete(){

        $act = \Yii::$app->request->get('act', false);
        $url_name = \Yii::$app->request->get('url_name', false);

        if ($act && $url_name) {
            $page = OtherPage::find()->where(['url_name' => $url_name])->one();
            if ($page) {
                $page->delete();
            }
        }

        $searchModel = new OtherPageSearch();

        $pagination = new Pagination([
            'pageSize' => \Yii::$app->request->get('per-page', 8),
            'page' => \Yii::$app->request->get('page', 1) - 1,
            'route' => '/admin/post/other-page-delete',
        ]);

        $dataProvider = $searchModel->search(\Yii::$app->request->get(), $pagination);

        return $this->render('otherPageDelete', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);

    }


    public function actionDeletePost(){

        $act = \Yii::$app->request->get('act', false);
        $id = \Yii::$app->request->get('id', false);

        if ($act && $id) {
            $page = Posts::find()->where(['id'=>$id])->one();
            if ($page) {
                $page->delete();
            }
        }

        $searchModel = new PostsSearch();

        $pagination = new Pagination([
            'pageSize' => \Yii::$app->request->get('per-page', 8),
            'page' => \Yii::$app->request->get('page', 1) - 1,
            'route' => '/admin/post/delete-post',
        ]);

        $dataProvider = $searchModel->search(\Yii::$app->request->get(), $pagination);

        return $this->render('deletePost', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);

    }

    public function actionAddBaner(){

        $params = [];

        if(\Yii::$app->request->isPost){
            $baner = new RightBaner();

            if($baner->load(\Yii::$app->request->post()) && $baner->save()){
                $params['toastMessage'] = [
                    'type' => 'success',
                    'message' => 'Банер изменен',
                ];
            }

        }else{
            $baner = RightBaner::find()->orderBy(['id'=>SORT_DESC])->one();

            if(!$baner){
                $baner = new RightBaner();
            }
        }

        $params['baner'] = $baner;


        return $this->render('addBaner',$params);

    }

}
