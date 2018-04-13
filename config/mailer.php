<?php

return [
    'class' => 'app\components\Mailer',
    'useFileTransport' => false,
    'transports' => [
        'info'     => [
            'class'      => 'Swift_SmtpTransport',
            'host'       => 'smtp.yandex.ru',
            'username'   => 'info@postim.by',
            'password'   => 'mc2447382@',
            'port'       => '465',
            'encryption' => 'ssl',
        ],
        'feedback' => [
            'class'      => 'Swift_SmtpTransport',
            'host'       => 'smtp.yandex.ru',
            'username'   => 'feedback@postim.by',
            'password'   => 'vlad1770015@',
            'port'       => '465',
            'encryption' => 'ssl',
        ],
        'ask' => [
            'class'      => 'Swift_SmtpTransport',
            'host'       => 'smtp.yandex.ru',
            'username'   => 'ask@postim.by',
            'password'   => 'vlad1770015@',
            'port'       => '465',
            'encryption' => 'ssl',
        ],
    ],
];