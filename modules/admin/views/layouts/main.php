<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use \yii\widgets\Pjax;
use \app\modules\admin\components\mainMenu\MainMenuWidget;


\app\assets\AppAdmAsset::register($this);
\app\assets\CustomScrollbarAsset::register($this);
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
        <a href="/" class="logo"></a>
        <div class="profile-icon-menu">
            <img class="round-img" src="<?=Yii::$app->user->identity->getPhoto()?>">
        </div>
    </div>
</div>
<!--верхнее меню end-->
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

<div class="container-blackout-popup-window" style="display: none"></div>

<div class="block-footer">
    <div class="block-footer-content">
        <div class="block-footer-btn">
            <ul class="menu-inline main-pjax">
                <li><a>О сайте</a></li>
                <li><a>Правила</a></li>
                <li><a>Соглашение</a></li>
                <li><a>Реклама</a></li>
                <li><a href="/feedback">Обратная связь</a></li>
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


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
