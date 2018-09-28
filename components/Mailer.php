<?php

namespace app\components;

use yii\base\InvalidConfigException;

class Mailer extends \yii\swiftmailer\Mailer
{

    private $sender;
    private $transports = [];

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->useTransport('info');
    }

    public function setTransports($transports)
    {
        if (!is_array($transports)) {
            throw new InvalidConfigException('"' . get_class($this) . '::transport" should be array, "' . gettype($transport) . '" given.');
        }
        $this->transports = $transports;
    }

    public function useTransport($name)
    {
        if (!array_key_exists($name, $this->transports)) {
            throw new InvalidConfigException('"' . get_class($this) . '::useTransport" The requested transport ' . $name . ' could not be found');
        }
        $this->setTransport($this->transports[$name]);
        $this->sender = $this->transports[$name]['username'];
        return $this;
    }

    public function compose($view = null, array $params = [])
    {
        $message = parent::compose($view, $params);
        $message->setFrom($this->sender); // By default SMTP Username will be used for sender
        return $message;
    }

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