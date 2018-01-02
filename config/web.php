<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'timeZone' => 'UTC',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log','assetMinifier'],
    'language' => 'ru-RU',
    'components' => [
        'assetMinifier' => [
            'class' => \lajax\assetminifier\Component::className(),
            'minifyCss' => true,
            'minifyJs' => true,
            'combine' =>true,
            'createGz' => true,
            'combiner' => [
                'class' => 'lajax\assetminifier\Combiner',
                'combinedFilesPath' => '/lajax-asset-minifier'
            ],
        ],
        'session' => [
            'class' => 'yii\web\DbSession', //'yii\web\DbSession',
            'sessionTable' => 'tbl_session',
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'enableCookieValidation' => false,
            'enableCsrfValidation' => false,
            'cookieValidationKey' => 'vVM9Giq1n12213dsmSiWysTpgZAwHs5',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'class' => 'app\components\User',
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => require(__DIR__ . '/mailer.php'),
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'assetManager' => [
            'appendTimestamp' => true,
        ],
        'urlManager' => [
            'normalizer' => [
                'class' => 'yii\web\UrlNormalizer',
                'normalizeTrailingSlash'=>true
            ],
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'Fotografii-<name:.+>-p<postId:\d+>' => 'post/gallery',
                'Sushi-sety-rolly-ot-sluzhby-dostavki-Sushi-Nashi' => 'test/index',
                'Skidki-<name:.+>-p<postId:\d+>' => 'post/get-discounts-by-post',
                'add/discount/<postId:\d+>' => 'discount/add',
                '<url:.+>-d<discountId:\d+>' => 'discount/read',
                'discount/<discountId:\d+>/order' => 'discount/order',

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
                '<url:.+>-n<id:\d+>'=>'news/news',//статья новости
                '<url:^add$>'=>'post/add',
                '<url:^edit>/<id:\d+>'=>'post/edit',
                'search/<text:.+>'=>'site/search',
                'feedback'=>'site/feedback',
                [
                    'class' => 'app\components\customUrlManager\OtherPageUrlRule', //лента отзывов
                ],
            ],
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'fileMap' => [
                        'app'       => 'app.php',
                        'app/locativus' =>'locativus.php',
                        'app/parental_slope' =>'parental_slope.php',
                        'app/singular' =>'singular.php',
                    ],
                ],
            ],
        ],

        'city'=>[
            'class' => 'app\components\City',
        ],
        'category'=>[
            'class' => 'app\components\Category',
        ],

        'formatter' => [
            'class' => 'app\components\Formatter',
            'timeZone' => 'UTC',
        ],

        'oldFormatter' => [
            'class' => 'app\components\OldFormatter',
            'timeZone' => 'UTC',
        ],

        'authClientCollection' => require ('authClientCollection.php'),

    ],
    'modules' => [
        'admin' => [
            'class' => 'app\modules\admin\Module',
        ],
    ],
    'params' => $params,
    'on afterAction' => function () {
        if (!Yii::$app->user->isGuest) {
           $user = Yii::$app->user->identity;
           $now = time();
           if ($user->last_visit + Yii::$app->params['user.lastVisitUpdatePeriod'] < $now) {
               $user->last_visit = $now;
               $user->save();
           }
        }
    },
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
