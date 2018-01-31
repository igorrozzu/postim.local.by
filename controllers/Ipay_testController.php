<?php

namespace app\controllers;
use app\components\MainController;
use app\services\ipay\EripServices;
use Yii;
use yii\di\Container;
use yii\web\Request;

class Ipay_testController extends MainController {

    /* @var  EripServices $_eripService */
    protected $_eripService;

    public function init()
    {
        parent::init();
        $containerDI = new Container();
        $this->_eripService = $containerDI->get('app\services\ipay\EripServices');
    }

    public function actionService_info()
    {
        $request = Yii::$app->request;

        if($request->isPost && $request->post('XML'))
        {
            $response = $this->_eripService->processInfo($request->post('XML'));
            return $this->asXml($response);
        }
    }

    public function actionTransaction_start(){
        $request = Yii::$app->request;

        if($request->isPost && $request->post('XML'))
        {
            $response = $this->_eripService->transactionStart($request->post('XML'));
            return $this->asXml($response);
        }
    }

    public function actionTransaction_result(){
        $request = Yii::$app->request;

        if($request->isPost && $request->post('XML'))
        {
            $response = $this->_eripService->transactionResult($request->post('XML'));
            return $this->asXml($response);
        }
    }

    public function actionStorn_start(){
        $request = Yii::$app->request;

        if($request->isPost && $request->post('XML'))
        {
            $response = $this->_eripService->stornStart($request->post('XML'));
            return $this->asXml($response);
        }
    }

    public function actionStorn_result(){
        $request = Yii::$app->request;

        if($request->isPost && $request->post('XML'))
        {
            $response = $this->_eripService->stornResult($request->post('XML'));
            return $this->asXml($response);
        }
    }

}