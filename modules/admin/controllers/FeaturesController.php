<?php

namespace app\modules\admin\controllers;

use app\components\Pagination;
use app\modules\admin\components\AdminDefaultController;
use app\modules\admin\models\CategoryFeatures;
use app\modules\admin\models\CategoryFeaturesSearch;
use app\modules\admin\models\Features;
use app\modules\admin\models\FeaturesSearch;
use app\modules\admin\models\UnderCategoryFeatures;
use app\modules\admin\models\UnderCategoryFeaturesSearch;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use Yii;

/**
 * Default controller for the `admin` module
 */
class FeaturesController extends AdminDefaultController
{
    /**
     * Renders the index view for the module
     * @return string
     */

    public function actionIndex()
    {


        $searchModel = new FeaturesSearch();

        $pagination = new Pagination([
            'pageSize' => Yii::$app->request->get('per-page', 30),
            'page' => Yii::$app->request->get('page', 1) - 1,
            'route' => '/admin/features/index',
            'selfParams' => [
                'sort' => true,
                'FeaturesSearch' => true,
            ],
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->get(), $pagination);

        return $this->render('index', ['dataProvider' => $dataProvider, 'searchModel' => $searchModel]);


    }

    public function actionDelete(string $id)
    {

        $features = Features::find()->where(['id' => $id])->one();

        if ($features) {
            $features->delete();
        }

        $searchModel = new FeaturesSearch();

        $pagination = new Pagination([
            'pageSize' => Yii::$app->request->get('per-page', 30),
            'page' => Yii::$app->request->get('page', 1) - 1,
            'route' => '/admin/features/index',
            'selfParams' => [
                'sort' => true,
            ],
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->get(), $pagination);

        return $this->render('index', ['dataProvider' => $dataProvider, 'searchModel' => $searchModel]);

    }

    public function actionChange(int $status, $id)
    {

        $features = Features::find()->where(['id' => $id])->one();

        if ($features) {
            $features->filter_status = $status;
            $features->setScenario(Features::$SCENARIO['main']);
            $features->update();
        }

        $searchModel = new FeaturesSearch();

        $pagination = new Pagination([
            'pageSize' => Yii::$app->request->get('per-page', 8),
            'page' => Yii::$app->request->get('page', 1) - 1,
            'route' => '/admin/features/index',
            'selfParams' => [
                'sort' => true,
            ],
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->get(), $pagination);

        return $this->render('index', ['dataProvider' => $dataProvider, 'searchModel' => $searchModel]);

    }

    public function actionBindCategoryAndFeatures()
    {

        $model = new CategoryFeatures();

        $modelUnder = new UnderCategoryFeatures();

        $params = [];


        if (Yii::$app->request->isPost) {

            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    $model = new CategoryFeatures();
                    $params['toastMessage'] = [
                        'type' => 'success',
                        'message' => 'Особенность добавлена в категорию',
                    ];
                } else {
                    $nameAttribute = key($model->getErrors());

                    $params['toastMessage'] = [
                        'type' => 'error',
                        'message' => $model->getFirstError($nameAttribute),
                    ];
                }
            }

            if ($modelUnder->load(Yii::$app->request->post())) {
                if ($modelUnder->save()) {
                    $modelUnder = new UnderCategoryFeatures();
                    $params['toastMessage'] = [
                        'type' => 'success',
                        'message' => 'Особенность добавлена в подкатегорию',
                    ];
                } else {
                    $nameAttribute = key($modelUnder->getErrors());

                    $params['toastMessage'] = [
                        'type' => 'error',
                        'message' => $modelUnder->getFirstError($nameAttribute),
                    ];
                }
            }

        }


        $params['model'] = $model;
        $params['modelUnder'] = $modelUnder;

        return $this->render('category_features', $params);

    }

    public function actionAdd()
    {

        $model = new Features();
        $model->setScenario(Features::$SCENARIO['main']);
        $model->filter_status = Features::$FILTER_STATUS['no'];
        $model->type = Features::$TYPE['regular'];

        $modelUnder = new Features();
        $modelUnder->setScenario(Features::$SCENARIO['under']);
        $modelUnder->setFormName('FeaturesUnder');
        $modelUnder->filter_status = Features::$FILTER_STATUS['no'];
        $modelUnder->type = Features::$TYPE['regular'];

        $params = [];

        if (Yii::$app->request->isPost) {

            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    $model = new Features([
                        'filter_status' => Features::$FILTER_STATUS['no'],
                        'type' => Features::$TYPE['regular'],
                    ]);
                    $model->setScenario(Features::$SCENARIO['main']);
                    $params['toastMessage'] = [
                        'type' => 'success',
                        'message' => 'Особенность добавлена',
                    ];
                }
            }

            if ($modelUnder->load(Yii::$app->request->post())) {
                if ($modelUnder->save()) {
                    $modelUnder = new Features([
                        'filter_status' => Features::$FILTER_STATUS['no'],
                        'type' => Features::$TYPE['regular'],
                    ]);
                    $modelUnder->setFormName('FeaturesUnder');
                    $modelUnder->setScenario(Features::$SCENARIO['under']);
                    $params['toastMessage'] = [
                        'type' => 'success',
                        'message' => 'Подособенность добавлена',
                    ];
                }
            }

        }

        $params['model'] = $model;
        $params['modelUnder'] = $modelUnder;

        return $this->render('add', $params);

    }

    public function actionDeleteCategoryAndFeatures()
    {

        $act = Yii::$app->request->get('act', false);
        $features_id = Yii::$app->request->get('features_id', false);
        $category_id = Yii::$app->request->get('category_id', false);
        $under_category_id = Yii::$app->request->get('under_category_id', false);

        if ($act) {

            if ($category_id) {
                $category = CategoryFeatures::find()
                    ->where(['category_id' => $category_id, 'features_id' => $features_id])->one();
                if ($category) {
                    $category->delete();
                }
            } elseif ($under_category_id) {
                $under_category = UnderCategoryFeatures::find()
                    ->where([
                            'under_category_id' => $under_category_id,
                            'features_id' => $features_id,
                        ]
                    )
                    ->one();

                if ($under_category) {
                    $under_category->delete();
                }
            }

        }


        $searchModel = new CategoryFeaturesSearch();

        $pagination = new Pagination([
            'pageSize' => Yii::$app->request->get('per-page', 8),
            'page' => Yii::$app->request->get('page', 1) - 1,
            'route' => '/admin/features/delete-category-and-features',
            'selfParams' => [
                'CategoryFeaturesSearch' => true,
                'sort' => true,
            ],
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->get(), $pagination);


        $searchModelUnder = new UnderCategoryFeaturesSearch();

        $pagination2 = new Pagination([
            'pageSize' => Yii::$app->request->get('per-page', 8),
            'page' => Yii::$app->request->get('page', 1) - 1,
            'route' => '/admin/features/delete-category-and-features',
            'selfParams' => [
                'UnderCategoryFeaturesSearch' => true,
                'dp-1-sort' => true,
            ],
        ]);

        $dataProviderUnder = $searchModelUnder->search(Yii::$app->request->get(), $pagination2);

        return $this->render('delete_category_features.php', [
            'dataProvider' => $dataProvider,
            'dataProviderUnder' => $dataProviderUnder,
            'searchModel' => $searchModel,
            'searchModelUnder' => $searchModelUnder,
        ]);


    }


}
