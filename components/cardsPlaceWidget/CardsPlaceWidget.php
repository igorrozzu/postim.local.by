<?php
/**
 * Created by PhpStorm.
 * User: jrborisov
 * Date: 8.7.17
 * Time: 20.30
 */

namespace app\components\cardsPlaceWidget;

use yii\base\Widget;
use yii\helpers\Html;


class CardsPlaceWidget extends Widget
{
    public $dataprovider;
    public $settings;

    public function run()
    {
        echo $this->render('index', [
            'dataprovider' => $this->dataprovider,
            'settings' => $this->settings,
        ]);
    }

    public static function renderCategories($categories,$city){
        $html='';
        $tagCategories=[];
        $url_city=$city['url_name']?'/'.$city['url_name']:'';
        if($categories && is_array($categories)){
            foreach ($categories as $category){
                $tagCategories[]=Html::a($category['name'],$url_city.'/'.$category['url_name']);
            }
        }
        $html=implode(', ',$tagCategories);
        return $html;
    }
}