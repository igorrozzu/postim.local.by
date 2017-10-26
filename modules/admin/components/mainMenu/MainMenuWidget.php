<?php
/**
 * Created by PhpStorm.
 * User: jrborisov
 * Date: 8.7.17
 * Time: 20.30
 */

namespace app\modules\admin\components\mainMenu;

use yii\base\Widget;
use app\modules\admin\components\mainMenu\Config;


class MainMenuWidget extends Widget
{
    public $memuData = [];

    public function init()
    {
        parent::init();

        $this->memuData = Config::getConfig();

    }

    public function run()
    {
        echo $this->render('index',['data'=>$this->memuData]);
    }
}