<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AuthUserAsset;
use app\assets\BusinessAccountAsset;
use app\assets\LoginFormsAsset;
use app\models\entities\NotificationUser;
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

BusinessAccountAsset::register($this);

$user = Yii::$app->user->identity;
$countNotif = NotificationUser::getCountNotifications();
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
    <?= Html::csrfMetaTags() ?>
    <title><?=$this->title?></title>
    <?php $this->head() ?>
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

</head>
<body>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NJT875T"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<?php $this->beginBody() ?>

<?=MainMenuWidget::widget(); ?>

<!--верхнее меню-->
<div class="container-header">
    <div class="header authorized">
        <div class="logo-menu">
            <a href="<?=Yii::$app->city->Selected_city['url_name']?'/'.Yii::$app->city->Selected_city['url_name']:'/'?>" class="logo"></a>
            <div class="menu-btn">
                <div class="menu-icon"></div>
                <div class="menu-text">Каталог</div>
                <div class="menu-arrows-icon"></div>
            </div>
        </div>
        <div class="select-city btn-select-city"><?=\Yii::$app->city->Selected_city['name']?></div>
        <noindex>
            <div class="main-pjax">
                <a href="/add" class="btn_br" rel="nofollow">Добавить место</a>
            </div>
        </noindex>
        <div class="profile-icon-menu">
            <img class="round-img" src="<?=$user->getPhoto()?>">
        </div>
        <div class="btn-notice btn-notice-<?=($countNotif === 0) ? 'un': '' ?>active">
            <?php if($countNotif > 0): ?>
                <span class="count-notice"><?=$countNotif?></span>
            <?php endif;?>
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
        <div class="container-item-menu visible">
            <a class="user_icon" href="<?=Url::to(['user/index', 'id'=>$user->getId()])?>">
                <span><img src="<?=$user->getPhoto()?>"></span>Мой профиль
            </a>
        </div>

        <?php if(Yii::$app->user->identity->hasOwner()):?>
            <div class="business-block">
                <div class="container-item-menu">
                    <a><span class="icon-business"></span>Бизнес аккаунт</a>
                </div>
                <div class="container-item-menu visible">
                    <a href="<?=Url::to(['account/order-promocodes'])?>"><span></span>Заказы промокодов</a>
                </div>
                <div class="container-item-menu visible">
                    <a href="<?=Url::to(['account/replenishment'])?>"><span></span>Пополнить счет</a>
                </div>
                <div class="container-item-menu visible">
                    <a href="<?=Url::to(['account/premium'])?>"><span></span>Премиум аккаунт</a>
                </div>
            </div>
        <?php endif;?>


        <div class="container-item-menu visible">
            <a href="<?=Url::to(['user/get-promocodes'])?>">
                <span class="icon-promotional"></span>Мои промокоды
            </a>
        </div>
        <div class="container-item-menu visible">
            <a href="<?=Url::to(['user/izbrannoe'])?>">
                <span class="icon-favorites"></span>Избранное
            </a>
        </div>
        <div class="container-item-menu visible">
            <a href="<?=Url::to(['user/settings'])?>">
                <span class="icon-settings"></span>Настройки
            </a>
        </div>
        <?php if(Yii::$app->user->isModerator()):?>
            <div class="container-item-menu visible">
                <a href="<?=Url::to(['admin/default/set-wewefwf','tokenWewefwf'=>Yii::$app->user->generationSecretToken()])?>"><span></span>Админка</a>
            </div>
        <?php endif;?>
        <div class="container-item-menu visible">
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

<noindex>
    <div class="block-footer">
        <div class="block-footer-content">
            <div class="block-footer-btn">
                <ul class="menu-inline main-pjax">
                    <li><a href="/about" rel="nofollow">О сайте</a></li>
                    <li><a href="/review-rules" rel="nofollow">Правила</a></li>
                    <li><a href="/agreement" rel="nofollow">Соглашение</a></li>
                    <li><a href="/business" rel="nofollow">Бизнес-аккаунт</a></li>
                    <li><a href="/feedback" rel="nofollow">Обратная связь</a></li>
                </ul>
                <div class="block-social-icons">
                    <div class="block-social">
                        <a href="https://vk.com/postimby" class="social-btn-vk" rel="nofollow"></a>
                        <a href="https://www.facebook.com/postimby" class="social-btn-fb" rel="nofollow"></a>
                        <a href="https://twitter.com/postimby" class="social-btn-tw" rel="nofollow"></a>
                    </div>
                    <div class="block-social">
                        <a href="https://www.ok.ru/postimby" class="social-btn-ok" rel="nofollow"></a>
                        <a href="https://www.instagram.com/postimby" class="social-btn-inst" rel="nofollow"></a>
                        <a href="https://chats.viber.com/postimby/ru" class="social-btn-viber" rel="nofollow"></a>
                    </div>
                </div>
            </div>
            <div class="text-footer">
                ИП&nbsp;Борисов&nbsp;Владислав&nbsp;Александрович, УНП&nbsp;591251086. Режим&nbsp;работы&nbsp;–&nbsp;9:00&nbsp;до&nbsp;18:00. Тел:&nbsp;(029)&nbsp;718&nbsp;16&nbsp;66. Эл.&nbsp;почта:&nbsp;<span class="email-address">info@postim.by.</span> ©&nbsp;2016–2017&nbsp;Postim.by
            </div>
        </div>
    </div>
</noindex>

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

]);
?>
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
