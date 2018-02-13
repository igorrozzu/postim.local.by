<?php

$manager = [
    'normalizer' => [
        'class' => 'yii\web\UrlNormalizer',
        'normalizeTrailingSlash'=>true
    ],
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'rules' => [
        'Fotografii-<name:.+>-p<postId:\d+>' => 'post/gallery',
        'Fotografiya-<name:.+>-f<idPhoto:\d+>' => 'post/photo',
        'Skidki-<name:.+>-p<postId:\d+>' => 'post/get-discounts-by-post',
        'add/discount/<postId:\d+>' => 'discount/add',
        '<url:.+>-d<discountId:\d+>' => 'discount/read',
        'discount/<discountId:\d+>/order' => 'discount/order',

        'print-order' => 'discount/print-order',
        'promocody' => 'user/get-promocodes',

        '<url:.+>-p<id:\d+>/moderation'=>'post/post-moderation',//модерируемая информация о месте
        '<url:.+>-p<id:\d+>'=>'post/index',//информация о месте
        'id<id:\d+>' => 'user/index',
        [
            'class' => 'app\components\customUrlManager\CityAndCategoryUrlRule',//лента категории
        ],
        [
            'class' => 'app\components\customUrlManager\NewsUrlRule', //лента новостей
        ],
        [
            'class' => 'app\components\customUrlManager\ReviewsUrlRule', //лента отзывов
        ],
        [
            'class' => 'app\components\customUrlManager\DiscountsUrlRule', //лента скидок
        ],
        '<url:.+>-n<id:\d+>'=>'news/news',//статья новости
        '<url:^add$>'=>'post/add',
        '<url:^edit>/<id:\d+>'=>'post/edit',
        'search/<text:.+>'=>'site/search',
        'feedback'=>'site/feedback',
        [
            'class' => 'app\components\customUrlManager\OtherPageUrlRule', //лента отзывов
        ],
        'business-account' => 'lading/sale-of-a-business-account'
    ],
];

if (php_sapi_name() == "cli") {
    $manager['baseUrl'] = 'https://postim.by';
}

return $manager;