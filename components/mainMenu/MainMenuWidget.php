<?php
/**
 * Created by PhpStorm.
 * User: jrborisov
 * Date: 8.7.17
 * Time: 20.30
 */

namespace app\components\mainMenu;

use app\models\Category;
use yii\base\Widget;
use ReflectionClass;


class MainMenuWidget extends Widget
{
    public $dataprovider=[];
    public $typeMenu = null;

    public static $leftMenu='leftMenu';
    public static $catalogMenu='catalogMenu';

    public function init()
    {
        parent::init();

        if($this->typeMenu === null){
            $this->typeMenu = self::$leftMenu;
        }

        if(!$this->dataprovider=\Yii::$app->cache->get('list_category_place')){

            $this->dataprovider = Category::find()
                ->joinWith('underCategory')
                ->orderBy(['tbl_under_category.name'=>1,'tbl_category.name'=>1  ])
                ->all();
            \Yii::$app->cache->add('list_category_place',$this->dataprovider,600);
        }

    }

    public function run()
    {
        echo $this->render('index',[
            'dataprovider'=>$this->dataprovider,
        ]);
    }
    public function getViewPath()
    {
        $class = new ReflectionClass($this);

        return dirname($class->getFileName()) . DIRECTORY_SEPARATOR . 'views'.'/'.$this->typeMenu;
    }
}