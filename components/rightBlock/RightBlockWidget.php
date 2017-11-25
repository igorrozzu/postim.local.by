<?php
namespace app\components\rightBlock;

use yii\base\Widget;

class RightBlockWidget extends Widget{


    public function run()
    {

        if(!$data = \Yii::$app->cache->get('right_baner')){
            $query = 'SELECT * FROM tbl_right_baner ORDER BY id DESC';
            $data = \Yii::$app->db->createCommand($query)->queryOne();
            \Yii::$app->cache->set('right_baner',$data,300);
        }

        echo $this->render('index',['data'=>$data]);
    }

}