<?php

namespace app\components;

use Yii;
use yii\helpers\Url;

class MetaTagsSocialNetwork
{
    public static function initOg($view, $params)
    {
        foreach ($params as $property => $content) {
            $view->registerMetaTag([
                'property' => $property,
                'content' => $content,
            ]);
        }
    }

    public static function registerOgTags($context, array $tags)
    {
        $defaultImgUrl = Yii::$app->request->getHostInfo() . '/default_img.jpg';

        $metaTagsOg = [
            'og:locale' => 'ru_RU',
            'og:type' => 'website',
            'og:url' => Url::to('', true),
            'og:site_name' => 'Postim.by',
            'twitter:site' => 'Postim.by',
            'og:image' => $defaultImgUrl,
            'twitter:image:src' => $defaultImgUrl,
        ];
        $metaTagsOg = array_merge($metaTagsOg, $tags);

        self::initOg($context, $metaTagsOg);
    }
}