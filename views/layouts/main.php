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
    <?= Html::csrfMetaTags() ?>
    <title><?=$this->title?></title>
    <?php $this->registerAssetBundle('yii\web\JqueryAsset',yii\web\View::POS_HEAD); ?>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<?=MainMenuWidget::widget(); ?>

<!--верхнее меню-->
<div class="container-header">
    <div class="header">
        <div class="menu-btn"></div>
        <a href="<?=Yii::$app->city->Selected_city['url_name']?'/'.Yii::$app->city->Selected_city['url_name']:'/'?>" class="logo"></a>
        <div class="select-city btn-select-city"><?=\Yii::$app->city->Selected_city['name']?></div>
        <div class="main-pjax">
            <a href="/add" class="btn_br">Добавить место</a>
        </div>
        <div class="sign_in_btn">Войти</div>
        <div class="main-pjax">
            <a href="/add" class="btn_add_place"></a>
        </div>
        <div class="search_block">
            <div class="cancel"></div>
            <input class="search" type="text" placeholder="Поиск" value="<?=Yii::$app->request->get('text','')?>">
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
    'scrollTo'=>1
]);
echo $content;
Pjax::end();

?>

<div class="block-footer">
    <div class="block-footer-content">
        <div class="block-footer-btn">
            <ul class="menu-inline main-pjax">
                <li><a href="/about">О сайте</a></li>
                <li><a href="/review-rules">Правила</a></li>
                <li><a href="/agreement">Соглашение</a></li>
                <li><a href="/business">Бизнес-аккаунт</a></li>
                <li><a href="/feedback">Обратная связь</a></li>
            </ul>
            <div class="block-social-icons">
                <div class="block-social">
                    <a href="https://vk.com/postimby" class="social-btn-vk"></a>
                    <a href="https://www.facebook.com/postimby" class="social-btn-fb"></a>
                    <a href="https://twitter.com/postimby" class="social-btn-tw"></a>
                </div>
                <div class="block-social">
                    <a href="https://www.ok.ru/postimby" class="social-btn-ok"></a>
                    <a href="https://www.instagram.com/postimby" class="social-btn-inst"></a>
                    <a href="https://chats.viber.com/postimby/ru" class="social-btn-viber"></a>
                </div>
            </div>
        </div>
        <div class="text-footer">
            ИП&nbsp;Борисов&nbsp;Владислав&nbsp;Александрович, УНП&nbsp;591251086. Режим&nbsp;работы&nbsp;–&nbsp;9:00&nbsp;до&nbsp;18:00. Тел:&nbsp;(029)&nbsp;718&nbsp;16&nbsp;66. Эл.&nbsp;почта:&nbsp;<span class="email-address">info@postim.by.</span> ©&nbsp;2016–2017&nbsp;Postim.by
        </div>
    </div>
</div>

<div class="container-blackout-popup-window"
    <?php if (!isset($this->params['form-message'])):?>
        style="display: none"
    <?php endif;?>>
    <?=$this->params['form-message'] ?? ''?>
</div>
<?=ListCityWidget::widget(['settings'=>
    [
    'id'=>'menu_list_city',
    'is_menu'=>true
    ]

]);?>

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
