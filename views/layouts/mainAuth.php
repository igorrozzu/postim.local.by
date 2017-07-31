<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AuthUserAsset;
use app\assets\LoginFormsAsset;
use app\models\Notification;
use yii\helpers\Html;
use app\assets\AppAsset;
use app\assets\CustomScrollbarAsset;
use app\components\mainMenu\MainMenuWidget;
use app\components\socialWidgets\SocialWidget;
use yii\helpers\Url;
use \yii\widgets\Pjax;
use \app\components\ListCityWidget\ListCityWidget;

AppAsset::register($this);
CustomScrollbarAsset::register($this);
AuthUserAsset::register($this);

$user = Yii::$app->user->identity;
$countNotif = Notification::getCountNotifications();
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
    <title><?=$this->title?></title>
    <?php $this->head() ?>
    <?php $this->registerAssetBundle('yii\web\JqueryAsset',yii\web\View::POS_HEAD); ?>
</head>
<body>
<?php $this->beginBody() ?>

<?=MainMenuWidget::widget(); ?>

<!--верхнее меню-->
<div class="container-header">
    <div class="header authorized">
        <div class="menu-btn"></div>
        <div class="logo"></div>
        <div class="select-city btn-select-city"><?=\Yii::$app->city->Selected_city['name']?></div>
        <div class="btn_br">Добавить место</div>
        <div class="profile-icon-menu">
            <img class="round-img" src="<?=$user->getPhoto()?>">
        </div>
        <div class="btn-notice btn-notice-<?=($countNotif === 0) ? 'un': '' ?>active">
            <?php if($countNotif > 0): ?>
            <span class="count-notice"><?=$countNotif?></span>
            <?php endif;?>
        </div>
        <div class="btn_add_place"></div>
        <div class="search_block">
            <input class="search" type="text" placeholder="Поиск">
            <span class="btn-search"></span>
        </div>
    </div>
</div>
<!--верхнее меню end-->
<!--уведомления-->
<div class="notif-menu">
    <div class="notif-header-menu">
        <div class="right-arrow"></div>
        <div class="header-menu-title">Уведомления</div>
    </div>
    <div class="notif-content">
        <div class="replace-notif-block" href="/notification/index">
            <div id="loader-box">
                <div class="loader"></div>
            </div>
        </div>
    </div>
</div>
<!--уведомления end-->
<!--профиль юзера-->
<div class="right-menu-profile main-pjax">
    <div class="container-header-right-menu">
        <div class="btn-close"></div>
    </div>
    <div class="container-body-right-menu">
        <div class="container-item-menu">
            <a class="user_icon" href="<?=Url::to(['user/index', 'id'=>$user->getId()])?>">
                <span><img src="<?=$user->getPhoto()?>"></span>Мой профиль
            </a>
        </div>
        <div class="container-item-menu active">
            <a class="close-right-menu-list" data-id-open="business"><span><img src="/img/icon-business.png"></span>Бизнес акаунт</a>
            <div class="menu-list open-list" id="business">
                <a><span></span>Заказы промокодов</a>
                <a><span></span>Заказы сертификатов</a>
            </div>
        </div>
        <div class="container-item-menu">
            <a><span><img src="/img/icon-big-purse.png"></span>Пополнить счет</a>
        </div>
        <div class="container-item-menu">
            <a href="<?=Url::to(['user/moi-promocody'])?>">
                <span><img src="/img/icon-promotional.png"></span>Мои промокоды
            </a>
        </div>
        <div class="container-item-menu">
            <a href="<?=Url::to(['user/moi-sertifikaty'])?>">
                <span><img src="/img/icon-certificates.png"></span>Мои сертификаты
            </a>
        </div>
        <div class="container-item-menu">
            <a href="<?=Url::to(['user/izbrannoe'])?>">
                <span><img src="/img/add-favorit-icon.png"></span>Избранное
            </a>
        </div>
        <div class="container-item-menu">
            <a><span><img src="/img/news-icon.png"></span>Все отзовы</a>
        </div>
        <div class="container-item-menu">
            <a href="<?=Url::to(['user/settings'])?>">
                <span><img src="/img/icon-filter.png"></span>Настройки
            </a>
        </div>
        <div class="container-item-menu">
            <a href="<?=Url::to(['site/logout'])?>"><span></span>Выход</a>
        </div>
    </div>
</div>
<!--профиль юзера end-->
<?php
Pjax::begin([
    'timeout' => 1000,
    'enablePushState' => true,
    'id' => 'main-view-container',
    'linkSelector' => '.main-pjax a',
    'formSelector' => false,
    'scrollTo'=>1
]);
echo $content;
Pjax::end();

?>

<?=SocialWidget::widget();?>

<div class="block-footer">
    <div class="block-footer-content">
        <div class="block-footer-btn">
            <ul class="menu-inline">
                <li><a>О сайте</a></li>
                <li><a>Правила</a></li>
                <li><a>Соглашение</a></li>
                <li><a>Реклама</a></li>
                <li><a>Обратная связь</a></li>
            </ul>
            <div class="block-social-icons">
                <div class="block-social">
                    <a class="social-btn-vk"></a>
                    <a class="social-btn-fb"></a>
                    <a class="social-btn-tw"></a>
                </div>
                <div class="block-social">
                    <a class="social-btn-ok"></a>
                    <a class="social-btn-inst"></a>
                    <a class="social-btn-viber"></a>
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
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
