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
    public static function sendConfirmMessage()
    {
        $user = Yii::$app->user->identity;
        return Yii::$app->mailer->compose(['html' => 'authToken'],
            ['user' => $user])
            ->setFrom(Yii::$app->params['mail.supportEmail'])
            ->setTo($user->email)
            ->setSubject(Yii::$app->params['mail.ConfirmMessageSubject'])
            ->send();
    }

    public static function sendPasswordResetMessage(User $user)
    {
        return Yii::$app->mailer->compose(['html' => 'passwordResetToken'],
            ['user' => $user])
            ->setFrom(Yii::$app->params['mail.supportEmail'])
            ->setTo($user->email)
            ->setSubject(Yii::$app->params['mail.PasswordResetMessageSubject'])
            ->send();
    }

    public static function sendSuccessRegisterThroughSocial(User $user, string $sendingPassword)
    {
        return Yii::$app->mailer->compose(['html' => 'successRegisterThroughSocial'], [
            'user' => $user,
            'password' => $sendingPassword
        ])->setFrom(Yii::$app->params['mail.supportEmail'])
            ->setTo($user->email)
            ->setSubject(Yii::$app->params['mail.SuccessRegisterSubject'])
            ->send();
    }
}