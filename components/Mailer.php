<?php

namespace app\components;

class Mailer extends \yii\swiftmailer\Mailer
{
    public function sendMultiple(array $messages)
    {
        if (YII_ENV == 'prod') {
            return parent::sendMultiple($messages);
        } else {
            return 0;
        }
    }

    public function send($message)
    {
        if (YII_ENV == 'prod') {
            return parent::send($message);
        } else {
            return 0;
        }
    }

    public function sendMessage($message)
    {
        if (YII_ENV == 'prod') {
            return parent::sendMessage($message);
        } else {
            return false;
        }
    }
}