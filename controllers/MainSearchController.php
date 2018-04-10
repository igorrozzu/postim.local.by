<?php
/**
 * Created by PhpStorm.
 * User: igorrozu
 * Date: 2/26/18
 * Time: 2:05 AM
 */

namespace app\controllers;

use app\components\MainController;
use app\services\search\MainSearchService;
use Yii;
use yii\di\Container;

class MainSearchController extends MainController
{
    /* @var  MainSearchService $mainSearchService */
    protected $mainSearchService;

    public function init()
    {
        $containerDI = new Container();
        $this->mainSearchService = $containerDI->get(MainSearchService::class);
        $this->mainSearchService->setText(Yii::$app->request->get('text'));
        parent::init();
    }

    public function actionAutoComplete()
    {
        $entities = $this->mainSearchService->getAutoCompleteData(5);
        return $this->renderAjax('__search_auto_complete', [
            'entities' => $entities
        ]);
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;

        if ($request->isAjax && !$request->get('_pjax', false)) {
            $widget = $this->mainSearchService->getWidgetMetaData($request);

            return $widget['class']::widget($widget['params']);
        } else {
            $widgets = $this->mainSearchService->getWidgetsMetaData($request);

            return $this->render('__search_feeds.php', [
                'widgets' => $widgets,
                'type' => $request->get('type_feed','post'),
                'url' => $request->getPathInfo(),
            ]);
        }
    }
}