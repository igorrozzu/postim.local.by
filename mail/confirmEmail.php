<?php
use yii\helpers\Html;

$confirmLink = Yii::$app->urlManager->createAbsoluteUrl([
    'user/confirm-account',
    'token' => Yii::$app->security->encryptByKey($user->id, Yii::$app->params['security.encryptionKey'])
]);
?>

<tr>
    <td>
        <p style="margin: 0px; padding: 32px 20px 26px 20px;">Здравствуйте, <?= Html::encode($user->name) ?>.</p>
        <p style="margin: 0px; padding: 0px 20px 20px 20px;">Нажмите на кнопку, чтобы подтвердить ваш email:</p>
        <a href="<?= $confirmLink ?>" style="text-decoration: none; display: block;margin-left: 20px; height: 42px;
width: 270px;" target="_blank">
            <span style="background-color: #CF4D43;
-webkit-text-size-adjust:none;
display: block;
border-bottom: 2px solid #a82828;
border-radius: 3px;
-webkit-border-radius: 3px;
-moz-border-radius: 3px;
height: 42px;
width: 270px;
color: #ffffff;
text-align: center;
line-height: 42px;
cursor: pointer;">Подтвердить email</span>
        </a>
    </td>
</tr>