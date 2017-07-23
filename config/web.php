<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'timeZone' => 'UTC',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
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
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.yandex.ru',
                'username' => 'info@postim.by',
                'password' => 'mc2447382@',
                'port' => '465',
                'encryption' => 'ssl',
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                'file' => [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['trace', 'info'],
                    'categories' => ['yii\*'],
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
                'id<id:\d+>' => 'user/index',
                [
                    'class' => 'app\components\customUrlManager\CityAndCategoryUrlRule',
                ]
            ],
        ],
        'city'=>[
            'class' => 'app\components\City',
        ],

        'authClientCollection' => require ('authClientCollection.php'),

    ],
    'params' => $params,
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
