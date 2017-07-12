<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
$hostName = $_SERVER['HTTP_ORIGIN'];
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
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
<body>
<?php $this->beginBody() ?>

<!--верхнее меню-->
<div style="width: 100%; background-color: #3c5994; height: 60px;">
    <div>
        <div style="margin-left: 20px; float: left;padding: 19px 0 20px 0;">
            <img src="<?= $hostName ?>/img/logo.png">
        </div>
    </div>
</div>
<!--верхнее меню end-->

<?=$content;?>
<div style="padding: 0px 20px 32px 20px;">
    <p style="margin-bottom: 3px;">Ecли вы получили это письмо по ошибке, просто проигнорируйте его</p>
    <p>С наилучшими пожеланиями, Postim.by</p>
</div>
<div style="width: 100%;
    background-color: #3c5994;
    padding-bottom: 25px;
    padding-top: 25px;">
    <div style="padding: 0px 20px;">
        <p style="color:white;font-family: PT_Sans bold;margin-bottom: 20px;">Присоединяйтесь к нам!</p>
        <div style="display: flex;flex-wrap: wrap;">
            <div style="display: flex;flex-wrap: wrap;margin-right: 60px;">
                <div style="display: flex;cursor: pointer;flex-wrap: wrap;align-items: center;">
                    <div>
                        <a href="#"><img src="<?= $hostName ?>/img/vk-icon.png" style="margin-right: 5px;
    display: inline-block;
    width: 30px;
    height: 26px;" ></a>
                        <a href="#"><img src="<?= $hostName ?>/img/fb-icon.png" style="margin-right: 5px;
    display: inline-block;
    width: 30px;
    height: 26px;" ></a>
                        <a href="#"><img src="<?= $hostName ?>/img/tw-icon.png" style="margin-right: 5px;
    display: inline-block;
    width: 30px;
    height: 26px;" ></a>
                        <a href="#"><img src="<?= $hostName ?>/img/ok-icon.png" style="margin-right: 5px;
    display: inline-block;
    width: 30px;
    height: 26px;" ></a>
                        <a href="#"><img src="<?= $hostName ?>/img/inst-icon.png" style="margin-right: 5px;
    display: inline-block;
    width: 30px;
    height: 26px;" ></a>
                        <a href="#"><img src="<?= $hostName ?>/img/viber-icon.png" style="margin-right: 5px;
    display: inline-block;
    width: 30px;
    height: 26px;" ></a>
                    </div>
                </div>
            </div>
            <div>
                <p style="color:white;
    font-family: PT_Sans bold;">Есть вопросы? Мы с радостью ответим на них.</p>
                <p style="color:white;
    font-family: PT_Sans bold;">Воспользуйтесь функционалом</p>
            </div>
        </div>
    </div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
