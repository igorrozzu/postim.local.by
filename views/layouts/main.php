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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <?= Html::csrfMetaTags() ?>
    <title></title>
    <?php $this->head() ?>
    <?php $this->registerAssetBundle('yii\web\JqueryAsset',yii\web\View::POS_HEAD); ?>
</head>
<body>
<?php $this->beginBody() ?>

<?=MainMenuWidget::widget(
        [
            'select_category'=>$this->context->category ?? false,
            'select_under_category'=>$this->context->under_category ?? false
        ]
); ?>

<!--верхнее меню-->
<div class="container-header">
    <div class="header">
        <div class="menu-btn"></div>
        <div class="logo"></div>
        <div class="select-city btn-select-city"><?=\Yii::$app->city->Selected_city['name']?></div>
        <div class="btn_br">Добавить место</div>
        <div class="sign_in_btn">Войти</div>
        <div class="btn_add_place"></div>
        <div class="search_block">
            <input class="search" type="text" placeholder="Поиск">
            <span class="btn-search"></span>
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
