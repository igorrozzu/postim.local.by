<?php
namespace app\components\rightBlock;

use yii\base\Widget;

class RightBlockWidget extends Widget{


    public function run()
    {
        echo $this->render('index');
    }

}