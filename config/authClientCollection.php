<?php
return [
    'class' => 'yii\authclient\Collection',
    'clients' => [
        'google' => [
            'class' => 'yii\authclient\clients\Google',
            'clientId' => '746824588698-qokodc56kbfhnu81e2jif58ev5m03ac9.apps.googleusercontent.com',
            'clientSecret' => 'pnl1lxNoCvPIOjMXXN2Gtc8Q',
        ],
        'facebook' => [
            'class' => 'yii\authclient\clients\Facebook',
            'clientId' => '898590366900281',
            'clientSecret' => '8915a29680089617d0c279357c0ce2fb',
            'scope' => ['email'],
            'attributeNames' => ['id', 'name', 'email', 'picture.type(large)']
        ],
        'twitter' => [
            'class' => 'yii\authclient\clients\Twitter',
            'attributeParams' => [
                'include_email' => 'true',
                'skip_status' => 'true',
                'include_entities' => 'false'
            ],
            'consumerKey' => 'IXuThMKRMMLS1s8tsKc6dlioq',
            'consumerSecret' => 'RcsL58rQyFTrBquk8L1w9b3fsfpzj0DBEbKIoO4LmoSTTUZR1x',
        ],
        'vkontakte' => [
            'class' => 'yii\authclient\clients\VKontakte',
            'clientId' => '5088327',
            'clientSecret' => 'Hn7Toih2jduSjZVtoLdl',
            'scope' => ['email'],
            'attributeNames' => ['photo_max_orig', 'photo_200', 'photo_max', 'photo_400_orig']
        ],
        'odnoklassniki' => [
            'class' => 'app\components\social\clients\Odnoklassniki',
            'applicationKey' => 'CBAPOJPFEBABABABA',
            'clientId' => '1157164544',
            'clientSecret' => 'F78F6B84D8E11B35C5D34933',
        ],

    ],
];