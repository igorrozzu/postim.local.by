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


class QuestionAnswerWidget extends Widget
{
    public $dataprovider=[];
    public $is_only_comments = false;


    public function run()
    {
        if(!$this->is_only_comments){
            echo $this->render('index',[
                'dataprovider'=>$this->dataprovider
            ]);
        }else{
            echo $this->render('item_comment',[
                'dataprovider'=>$this->dataprovider,
            ]);
        }

    }
    public function getViewPath()
    {
        $class = new ReflectionClass($this);

        return dirname($class->getFileName()) . DIRECTORY_SEPARATOR .'views/question_answer';
    }
}