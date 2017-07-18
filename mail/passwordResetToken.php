<?php
use yii\helpers\Html;


$resetLink = Yii::$app->urlManager->createAbsoluteUrl([
    'site/reset-password',
    'token' => $user->password_reset_token
]);

?>

<tr>
    <td>
        <p style="margin: 0px; padding: 32px 20px 26px 20px;">Здравствуйте, <?= Html::encode($user->name) ?>.</p>
        <p style="margin: 0px; padding: 0px 20px 20px 20px;">Для восстановления пароля нажмите на кнопку:</p>
        <a href="<?= $resetLink ?>" style="text-decoration: none; display: block;margin-left: 20px; height: 42px;
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
cursor: pointer;">Восстановить пароль</span>
        </a>
    </td>
</tr>
