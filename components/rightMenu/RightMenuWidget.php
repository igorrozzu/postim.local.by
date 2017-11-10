<?php
/**
 * Created by PhpStorm.
 * User: jrborisov
 * Date: 8.7.17
 * Time: 20.30
 */

namespace app\components\rightMenu;

use app\models\OtherPage;
use yii\base\Widget;



class RightMenuWidget extends Widget
{
    public $list = [];

    public function init()
    {
        parent::init();

        $this->list = OtherPage::find()
            ->where(['status'=>OtherPage::$STATUS['showMenu']])
            ->orderBy(['id'=>SORT_ASC])
            ->all();

    }

    public function run()
    {
        echo $this->render('index',[
            'list'=>$this->list,
        ]);
    }
}