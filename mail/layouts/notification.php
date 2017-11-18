<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
$hostName = Yii::$app->params['site.hostName'];
?>
<?php $this->beginPage() ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html lang="<?= Yii::$app->language ?>" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <?= Html::csrfMetaTags() ?>
    <title></title>
    <?php $this->head() ?>

</head>
<body style="margin: 0; padding: 0; background-color: #f1f2f4">
<?php $this->beginBody() ?>
<table border="0" cellpadding="0" cellspacing="0" style="margin: 0 auto; padding-top: 20px; max-width: 600px;
        font-family: Tahoma, Verdana, Helvetica, sans-serif; color: #444444; font-size: 15px;" >
    <tr>
        <td style="background-color: #3c5994; height: 60px;">
            <img style="display:block; margin-left: 20px; color: #FFFFFF;margin-top: 20px" alt="Postim.by" border="0"
                 src="<?= $hostName ?>/img/logo.png">
        </td>
    </tr>

    <?=$content;?>

    <tr>
        <td style="padding: 23px 20px 30px 20px; background-color: #FFFFFF">
            <span style="display: block; margin: 0px;">С наилучшими пожеланиями, Postim.by</span>
        </td>
    </tr>
    <tr style="display: block; background-color: #F2F4FD; padding: 20px; border-bottom: 2px solid #CDCED0">
        <td>
            <span style="display: inline-block; width: 231px; margin-right: 54px;">
                <span style="display: block; margin-bottom: 20px;">Присоединяйтесь к нам!</span>

            <a href="https://vk.com/postimby" target="_blank" style="-webkit-text-size-adjust:none; text-decoration: none;">
                <img src="<?= $hostName ?>/img/vk-icon.png" alt="Vk" border="0" style="margin-right: 5px;
    display: inline-block;
    width: 30px;
    height: 26px;" >
            </a>
            <a href="https://www.facebook.com/postimby" target="_blank" style="-webkit-text-size-adjust:none; text-decoration: none; ">
                <img src="<?= $hostName ?>/img/fb-icon.png" alt="Fb" border="0" style="margin-right: 5px;
    display: inline-block;
    width: 30px;
    height: 26px;" >
            </a>
            <a href="https://twitter.com/postimby" target="_blank" style="-webkit-text-size-adjust:none; text-decoration: none; ">
                <img src="<?= $hostName ?>/img/tw-icon.png" alt="Tw" border="0" style="margin-right: 5px;
    display: inline-block;
    width: 30px;
    height: 26px;" >
            </a>
            <a href="https://www.ok.ru/postimby" target="_blank" style="-webkit-text-size-adjust:none; text-decoration: none; ">
                <img src="<?= $hostName ?>/img/ok-icon.png" alt="Ok" border="0" style="margin-right: 5px;
    display: inline-block;
    width: 30px;
    height: 26px;" >
            </a>
            <a href="https://www.instagram.com/postimby" target="_blank" style="-webkit-text-size-adjust:none; text-decoration: none; ">
                <img src="<?= $hostName ?>/img/inst-icon.png" alt="In" border="0" style="margin-right: 5px;
    display: inline-block;
    width: 30px;
    height: 26px;" >
            </a>
            <a href="https://chats.viber.com/postimby/ru" target="_blank" style="-webkit-text-size-adjust:none; text-decoration: none; ">
                <img src="<?= $hostName ?>/img/viber-icon.png" alt="Vb" border="0" style="margin-right: 5px;
    display: inline-block;
    width: 30px;
    height: 26px;" >
            </a>
            </span>
            <span style="display: inline-block; font-size: 11px;  width: 270px; margin-top: 20px;">
              <span style="-webkit-text-size-adjust:none">Есть вопрос? Мы с радостью на него ответим.</span><br>
                <span style="-webkit-text-size-adjust:none">
                    Воспользуйтесь функционалом &laquo;<a href="<?= $hostName ?>/feedback" target="_blank" style="color: #3C5994;">Обратной связи</a>&raquo;
                </span>
            </span>
        </td>
    </tr>
    <tr style="display: block; padding: 15px 0 20px 0;">
        <td style="display: block;">
            <span style="-webkit-text-size-adjust:none; font-size: 11px; display: block;
    text-align: center;">
                Вы можете ограничить или отменить уведомления по эл. почте в
                <a href="<?= $hostName ?>/user/settings" target="_blank" style="color: #3C5994;">настройках профиля</a>
            </span>
        </td>
    </tr>
</table>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
