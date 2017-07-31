<?php
/**
 * Created by PhpStorm.
 * User: jrborisov
 * Date: 8.7.17
 * Time: 20.30
 */

namespace app\components\cardsNewsWidget;

use yii\base\Widget;


class CardsNewsWidget extends Widget
{
    public $dataprovider;
    public $settings;

    public function run()
    {
        if($this->settings['last-news']??false){
            echo $this->render('index',[
                'dataprovider'=>$this->dataprovider,
                'settings'=>$this->settings
            ]);
        }else{
            echo $this->render('feed_index',[
                'dataprovider'=>$this->dataprovider,
                'settings'=>$this->settings
            ]);
        }

    }
}