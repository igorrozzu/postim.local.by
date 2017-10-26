<?php

namespace app\modules\admin\controllers;

use app\modules\admin\components\AdminDefaultController;
use app\modules\admin\models\DescriptionPage;
use yii\web\Controller;

/**
 * Default controller for the `admin` module
 */
class EditPageController extends AdminDefaultController
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $editPage = new DescriptionPage();
        $editPage->setScenario(DescriptionPage::$FIND_PAGE);
        if(\Yii::$app->request->isPost){
            if($editPage->load(\Yii::$app->request->post()) && $editPage->validate()){

                if($findPage = $editPage->getData()){
                    $findPage->find_url = $editPage->find_url;
                    $editPage = $findPage;
                }

                return $this->render('__save_form',['editPage'=>$editPage]);
            }else{
                return $this->render('index',['editPage'=>$editPage]);
            }

        }else{
            return $this->render('index',['editPage'=>$editPage]);
        }

    }

    public function actionSave(){

        $editPage = new DescriptionPage();
        $editPage->setScenario(DescriptionPage::$ADD_PAGE);

        if(\Yii::$app->request->isPost){

            if($editPage->load(\Yii::$app->request->post())){

                if($editPage->validate()){
                    $editPage->save();
                }else{
                    $editPage = $editPage->getData();
                    $editPage->setScenario(DescriptionPage::$EDIT_PAGE);
                    $editPage->load(\Yii::$app->request->post());
                    $editPage->update();
                }

                $editPage = new DescriptionPage();
                return $this->render('index',['editPage'=>$editPage]);
            }


        }else{
            return $this->render('index',['editPage'=>$editPage]);
        }
    }
}
