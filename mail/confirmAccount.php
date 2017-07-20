<?php
use yii\helpers\Html;

$confirmLink = Yii::$app->urlManager->createAbsoluteUrl([
    'site/confirm-account',
    'token' => Yii::$app->security->encryptByKey($user->id, Yii::$app->params['security.encryptionKey'])
]);
?>

<tr style="background-color: #FFFFFF">
    <td>
        <span style="display: block; margin: 32px 20px 1px 20px;">
            Здравствуйте, <?= Html::encode($user->name) ?>.
        </span>
        <span style="display: block; margin: 0px 20px 25px 20px;">
            Спасибо за регистрацию на сайте Postim.by!
        </span>
        <span style="display: block; margin: 0px 20px 19px 20px;">
            Нажмите на кнопку, чтобы подтвердить ваш email и завершить регистрацию:
        </span>
        <a href="<?=$confirmLink?>" style="text-decoration: none; display: block;margin:0px 0px 2px 20px; height: 42px;
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