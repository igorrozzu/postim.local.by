<?php

return [
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
];