<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\LoginFormsAsset;
use yii\helpers\Html;
use app\assets\AppAsset;
use app\assets\CustomScrollbarAsset;
use app\components\mainMenu\MainMenuWidget;
use app\components\socialWidgets\SocialWidget;
use \app\components\ListCityWidget\ListCityWidget;
use \yii\widgets\Pjax;

AppAsset::register($this);
CustomScrollbarAsset::register($this);
LoginFormsAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/css/lading/sale-of-business.css">
    <?= Html::csrfMetaTags() ?>
    <title><?=$this->title?></title>
    <?php $this->registerAssetBundle('yii\web\JqueryAsset',yii\web\View::POS_HEAD); ?>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-NJT875T');</script>
    <!-- End Google Tag Manager -->

    <!--push start-->
    <script charset="UTF-8" src="//cdn.sendpulse.com/28edd3380a1c17cf65b137fe96516659/js/push/8112939af3b63172ed5cc9252b64b0d9_1.js" async></script>
    <!--push end-->

    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NJT875T"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->


<!--верхнее меню-->
<div class="container-header">
    <div class="header">
        <a href="<?=Yii::$app->city->Selected_city['url_name']?'/'.Yii::$app->city->Selected_city['url_name']:'/'?>" class="logo-lading"></a>
        <div class="nav-content-menu">
            <div class="item-content-menu" data-for-selector="#for-bsn"><span class="active btn-forBsn">для Бизнеса</span></div>
            <div class="item-content-menu" data-for-selector="#instument"><span class="btn-instument">Инструмены</span></div>
            <div class="item-content-menu" data-for-selector="#price"><span class="btn-price">Стоимость</span></div>
        </div>
        <div class="block-main-right-btn">
            <div style="float: right" class="js-btn-active-bsa btn-border --border-blue" data-type="20">Подключить</div>
        </div>
    </div>
</div>
<!--верхнее меню end-->
<?php
Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => true,
    'id' => 'main-view-container',
    'linkSelector' => '.main-pjax a',
    'formSelector' => false,
    'scrollTo'=>1
]);
echo $content;
Pjax::end();
?>
<?= $this->render('footer')?>

<div class="container-blackout-popup-window" style="display: none"></div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
