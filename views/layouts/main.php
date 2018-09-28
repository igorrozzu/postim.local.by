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
use \app\widgets\canonicalHref\CanonicalHref;

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
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <?= Html::csrfMetaTags() ?>
    <title><?= $this->title ?></title>
    <?php $this->registerAssetBundle('yii\web\JqueryAsset', yii\web\View::POS_HEAD); ?>

    <!-- Google Tag Manager -->
    <script>(function (w, d, s, l, i) {
				w[l] = w[l] || [];
				w[l].push({
					'gtm.start':
							new Date().getTime(), event: 'gtm.js'
				});
				var f = d.getElementsByTagName(s)[0],
						j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
				j.async = true;
				j.src =
						'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
				f.parentNode.insertBefore(j, f);
			})(window, document, 'script', 'dataLayer', 'GTM-NJT875T');</script>
    <!-- End Google Tag Manager -->

    <!--push start-->
    <script charset="UTF-8"
            src="//cdn.sendpulse.com/28edd3380a1c17cf65b137fe96516659/js/push/8112939af3b63172ed5cc9252b64b0d9_1.js"
            async></script>
    <!--push end-->

    <?= CanonicalHref::widget(); ?>

    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<!-- Google Tag Manager (noscript) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NJT875T"
            height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->

<?= MainMenuWidget::widget(); ?>

<!--верхнее меню-->
<div class="container-header">
    <div class="header">
        <div class="logo-menu">
            <a href="<?= Yii::$app->city->Selected_city['url_name'] ? '/' . Yii::$app->city->Selected_city['url_name'] : '/' ?>"
               class="logo"></a>
            <div class="menu-btn">
                <div class="menu-icon"></div>
                <div class="menu-text">Каталог</div>
                <div class="menu-arrows-icon"></div>
            </div>
        </div>
        <div class="select-city btn-select-city"><?= \Yii::$app->city->Selected_city['name'] ?></div>
        <noindex>
            <div class="main-pjax">
                <a href="/add" class="btn_br" rel="nofollow">Добавить место
                    <span style="font-family: PT_Sans;">&nbsp;(бесплатно)</span>
                </a>
            </div>
            <div class="sign_in_btn">Войти</div>
        </noindex>
        <div class="search_block">
            <div class="cancel"></div>
            <input class="search" type="text" placeholder="Поиск" value="<?= Yii::$app->request->get('text', '') ?>">
            <span class="btn-search"></span>
        </div>
    </div>
</div>
<div class="block-auto-complete-search"></div>
<!--верхнее меню end-->
<?php
Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => true,
    'id' => 'main-view-container',
    'linkSelector' => '.main-pjax a',
    'formSelector' => false,
    'scrollTo' => 1,
]);
echo $content;
Pjax::end();

?>
<?= $this->render('footer') ?>

<div class="container-blackout-popup-window"
    <?php if (!isset($this->params['form-message'])): ?>
        style="display: none"
    <?php endif; ?>>
    <?= $this->params['form-message'] ?? '' ?>
</div>
<?= ListCityWidget::widget([
    'settings' =>
        [
            'id' => 'menu_list_city',
            'is_menu' => true,
        ],

]); ?>

<div class="left-menu filter">
    <div class="header-menu">
        <div class="header-menu-title">Все фильтры</div>
        <div class="left-arrow close-menu"></div>
    </div>
    <div class="menu-content">

    </div>
    <div class="bottom-btn filter-complete"><span>Готово</span></div>
</div>
<div class="left-menu under-filter">
    <div class="header-menu">
        <div class="header-menu-title">Все фильтры</div>
        <div class="left-arrow close-menu"></div>
    </div>
    <div class="menu-content">

    </div>
    <div class="bottom-btn filter-complete"><span>Готово</span></div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
