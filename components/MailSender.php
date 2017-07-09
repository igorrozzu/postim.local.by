<?php
/**
 * Created by PhpStorm.
 * User: igorrozu
 * Date: 7/8/17
 * Time: 11:33 PM
 */

namespace app\components;


use Yii;

class MailSender
{
    public static function sendConfirmMessage()
    {
        return Yii::$app->mailer->compose(['html' => 'authToken'],
            ['user' => Yii::$app->user->identity])
            ->setFrom(Yii::$app->params['mail.supportEmail'])
            ->setTo(Yii::$app->user->identity->email)
            ->setSubject(Yii::$app->params['mail.ConfirmMessageSubject'])
            ->send();
    }

    public static function sendPasswordResetMessage($user)
    {
        return Yii::$app->mailer->compose(['html' => 'passwordResetToken'],
            ['user' => $user])
            ->setFrom(Yii::$app->params['mail.supportEmail'])
            ->setTo($user->email)
            ->setSubject(Yii::$app->params['mail.PasswordResetMessageSubject'])
            ->send();
    }
}