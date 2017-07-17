<?php
use yii\helpers\Html;

$confirmLink = Yii::$app->urlManager->createAbsoluteUrl([
    'user/confirm-account',
    'token' => Yii::$app->security->encryptByKey($user->id, Yii::$app->params['security.encryptionKey'])
]);
?>

<div style="padding: 32px 20px;">
    <p style="margin-bottom: 3px;">Здравствуйте, <?= Html::encode($user->name) ?>.</p>
    <p style="margin-bottom: 20px;">Нажмите на кнопку, чтобы подтвердить ваш email:</p>
    <a href="<?= $confirmLink ?>" style="text-decoration: none;"><div style="background-color: #cf4d43;
    box-shadow: 0 2px 0 #a82828;
    border-radius: 3px;
    height: 42px;
    width: 270px;
    color: #ffffff;
    text-align: center;
    line-height: 42px;
    cursor: pointer;
    font-size: 16px;
    font-family: PT_Sans bold;">Подтвердить email</div></a>
</div>