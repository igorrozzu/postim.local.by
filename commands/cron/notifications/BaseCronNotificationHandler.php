<?php

namespace app\commands\cron\notifications;

use yii\mail\MailerInterface;

abstract class BaseCronNotificationHandler
{
    /**
     * @var
     */
    public $params;
    /**
     * @var MailerInterface
     */
    protected $mailer;

    /**
     * BaseCronNotificationHandler constructor.
     * @param MailerInterface $mailer
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        $this->mailer->htmlLayout = 'layouts/notification';
    }
}