<?php
namespace app\components;

class MetaTagsSocialNetwork{


    public static function initOg($view, $params){


        foreach ($params as $property =>$content){
            $view->registerMetaTag([
                'property' => $property,
                'content'=> $content
            ]);
        }

    }

}