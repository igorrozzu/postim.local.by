<?php
use yii\helpers\Html;


$resetLink = Yii::$app->urlManager->createAbsoluteUrl([
    'site/reset-password',
    'token' => $user->password_reset_token
]);

?>

<div style="padding: 32px 20px;">
    <p style="margin-bottom: 26px;">Здравствуйте, <?= Html::encode($user->name) ?>.</p>
    <p style="margin-bottom: 20px;">Для восстановления пароля нажмите на кнопку</p>
    <a href="<?= $resetLink ?>" style="text-decoration: none;"><div style="background-color: #cf4d43;
    box-shadow: 0 2px 0 #a82828;
    border-radius: 3px;
    height: 42px;
    width: 270px;
    color: #ffffff;
    text-align: center;
    line-height: 42px;
    cursor: pointer;
    font-size: 16px;
    font-family: PT_Sans bold;">Восстановить пароль</div></a>
</div>
