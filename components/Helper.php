<?php
namespace app\components;

use app\models\TotalView;
use Yii;

class Helper{

    public static function addViews(TotalView $totalView){
        $session = Yii::$app->session;

        if($session->get('totalView_'.$totalView['id'])==null){
            $totalView->updateCounters(['count' => 1]);
            $session->set('totalView_'.$totalView['id'],true);
        }
    }
}