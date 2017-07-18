<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
$hostName = Yii::$app->request->getHostInfo();
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
<body style="margin: 0; padding: 0; max-width: 600px; width: 100%;">
<?php $this->beginBody() ?>
<table border="0" cellpadding="0" cellspacing="0" style="margin:0; padding:0; width: 100%;
        font-family: Tahoma, Verdana, Helvetica, sans-serif; color: #444444; font-size: 15px;" >
    <tr>
        <td style="background-color: #3c5994; height: 60px;">
            <img style="display:block; margin-left: 20px; color: #FFFFFF;" alt="Postim.by" border="0"
                 src="<?= $hostName ?>/img/logo.png">
        </td>
    </tr>

    <?=$content;?>

    <tr>
        <td style="padding: 0px 20px 32px 20px;">
            <p style="margin-bottom: 1px;">Ecли вы получили это письмо по ошибке, просто проигнорируйте его.</p>
            <p style="margin: 0px;">С наилучшими пожеланиями, Postim.by</p>
        </td>
    </tr>
    <tr style="display: block; background-color: #F2F4FD; padding: 20px; border-bottom: 2px solid #CDCED0">
        <td style="padding-right: 50px;">
            <p style="margin-bottom: 20px;">Присоединяйтесь к нам!</p>

            <a href="#" target="_blank" style="-webkit-text-size-adjust:none; text-decoration: none;">
                <img src="<?= $hostName ?>/img/vk-icon.png" alt="Vk" border="0" style="margin-right: 5px;
    display: inline-block;
    width: 30px;
    height: 26px;" >
            </a>
            <a href="#" target="_blank" style="-webkit-text-size-adjust:none; text-decoration: none; ">
                <img src="<?= $hostName ?>/img/fb-icon.png" alt="Facebook" border="0" style="margin-right: 5px;
    display: inline-block;
    width: 30px;
    height: 26px;" >
            </a>
            <a href="#" target="_blank" style="-webkit-text-size-adjust:none; text-decoration: none; ">
                <img src="<?= $hostName ?>/img/tw-icon.png" alt="Twitter" border="0" style="margin-right: 5px;
    display: inline-block;
    width: 30px;
    height: 26px;" >
            </a>
            <a href="#" target="_blank" style="-webkit-text-size-adjust:none; text-decoration: none; ">
                <img src="<?= $hostName ?>/img/ok-icon.png" alt="Ok" border="0" style="margin-right: 5px;
    display: inline-block;
    width: 30px;
    height: 26px;" >
            </a>
            <a href="#" target="_blank" style="-webkit-text-size-adjust:none; text-decoration: none; ">
                <img src="<?= $hostName ?>/img/inst-icon.png" alt="Instagram" border="0" style="margin-right: 5px;
    display: inline-block;
    width: 30px;
    height: 26px;" >
            </a>
            <a href="#" target="_blank" style="-webkit-text-size-adjust:none; text-decoration: none; ">
                <img src="<?= $hostName ?>/img/viber-icon.png" alt="Viber" border="0" style="margin-right: 5px;
    display: inline-block;
    width: 30px;
    height: 26px;" >
            </a>
        </td>
        <td style="font-size: 11px; padding-top: 40px;">
            <span style="-webkit-text-size-adjust:none">Есть вопрос? Мы с радостью на него ответим.</span><br>
            <span style="-webkit-text-size-adjust:none">
                    Воспользуйтесь функционалом &laquo;<a href="#" target="_blank" style="color: #3C5994;">Обратной связи</a>&raquo;
                </span>
        </td>

    </tr>
</table>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
