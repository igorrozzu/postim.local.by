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


class CommentsPostsWidget extends Widget
{
    public $dataprovider = [];
    public $totalComments = 0;
    public $is_only_comments = false;
    public $is_official_user = false;


    public function run()
    {
        if (!$this->is_only_comments) {
            echo $this->render('index', [
                'dataprovider' => $this->dataprovider,
                'totalComments' => $this->totalComments,
                'is_official_user' => $this->is_official_user,
            ]);
        } else {
            echo $this->render('item_comment', [
                'dataprovider' => $this->dataprovider,
                'totalComments' => $this->totalComments,
            ]);
        }

    }

    public function getViewPath()
    {
        $class = new ReflectionClass($this);

        return dirname($class->getFileName()) . DIRECTORY_SEPARATOR . 'views/posts';
    }
}