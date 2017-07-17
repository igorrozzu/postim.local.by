<?php
/**
 * Created by PhpStorm.
 * User: igorrozu
 * Date: 7/8/17
 * Time: 11:33 PM
 */

namespace app\components;

use app\models\User;
use Yii;

class MailSender
{
    private $user;
    /**
     * MailSender constructor.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    public function sendConfirmMessage(string $view)
    {
        return Yii::$app->mailer->compose(['html' => $view], [
            'user' => $this->user
        ])
            ->setFrom(Yii::$app->params['mail.supportEmail'])
            ->setTo($this->user->email)
            ->setSubject(Yii::$app->params['mail.ConfirmMessageSubject'])
            ->send();
    }

    public function sendPasswordResetMessage()
    {
        return Yii::$app->mailer->compose(['html' => 'passwordResetToken'], [
            'user' => $this->user
        ])
            ->setFrom(Yii::$app->params['mail.supportEmail'])
            ->setTo($this->user->email)
            ->setSubject(Yii::$app->params['mail.PasswordResetMessageSubject'])
            ->send();
    }

}