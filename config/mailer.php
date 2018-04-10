<?php

return [
    'class' => 'app\components\Mailer',
    'useFileTransport' => false,
    'transport' => [
        'class' => 'Swift_SmtpTransport',
        'host' => 'smtp.yandex.ru',
        'username' => 'feedback@postim.by',
        'password' => 'vlad1770015@',
        'port' => '465',
        'encryption' => 'ssl',
    ],
];