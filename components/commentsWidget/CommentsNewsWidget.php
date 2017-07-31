<?php
/**
 * Created by PhpStorm.
 * User: jrborisov
 * Date: 8.7.17
 * Time: 20.30
 */

namespace app\components\commentsWidget;

use yii\base\Widget;
use ReflectionClass;


class CommentsNewsWidget extends Widget
{
    public $dataprovider=[];


    public function run()
    {
        echo $this->render('index',[
            'dataprovider'=>$this->dataprovider,
        ]);
    }
    public function getViewPath()
    {
        $class = new ReflectionClass($this);

        return dirname($class->getFileName()) . DIRECTORY_SEPARATOR .'views/news';
    }
}