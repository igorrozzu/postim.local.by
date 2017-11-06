<?php

namespace app\modules\admin\controllers;

use app\components\Pagination;
use app\modules\admin\components\AdminDefaultController;
use app\modules\admin\models\Category;
use app\modules\admin\models\CategorySearch;
use app\modules\admin\models\News;
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

}
