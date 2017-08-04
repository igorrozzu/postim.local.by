<?php
use app\components\cardsPlaceWidget\CardsPlaceWidget;
use app\components\cardsReviewsWidget\CardsReviewsWidget;
use yii\helpers\Url;
use yii\widgets\Pjax;
?>
<div class="margin-top60"></div>
<div class="block-content">
    <div class="bread-crumb">
        <a class="pre" href="#">Главная</a>
        <span class="separator"></span>
        <p>Все отзывы в <?=$region?></p>
    </div>
    <h1 class="h1-v">Все отзывы в <?=$region?></h1>
</div>
<?php
Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => false,
    'id' => 'feeds-of-user',
    'linkSelector' => '.feeds-btn-bar a',
    'formSelector' => false,
])
?>
<div class="block-flex-white">
    <div class="block-content">
        <div class="menu-btns-card feeds-btn-bar">
            <a href="<?=Url::to(['user/vse-otzyvy'])?>">
                <div class="btn2-menu <?=($type === 'all') ? 'btn2-menu-active' : ''?>">
                    Все
                </div>
            </a>
            <a href="<?=Url::to(['user/vse-otzyvy', 'type' => 'positive'])?>">
                <div class="btn2-menu <?=($type === 'positive') ? 'btn2-menu-active' : ''?>">
                    Положительные
                </div>
            </a>
            <a href="<?=Url::to(['user/vse-otzyvy', 'type' => 'negative'])?>">
                <div class="btn2-menu <?=($type === 'negative') ? 'btn2-menu-active' : ''?>">
                    Отрицательные
                </div>
            </a>
        </div>
    </div>
</div>

<div class="block-content">
    <?php if($dataProvider->getTotalCount()):?>
        <div class="">
            <?= CardsReviewsWidget::widget([
                'dataProvider' => $dataProvider,
                'settings' => [
                    'show-more-btn' => true,
                    'replace-container-id' => 'feed-all-reviews',
                    'load-time' => $loadTime,
                ]
            ]); ?>
    <?php else:?>
        <div class="card-promo">
            <p class="card-text-notice">Отзывов не найдено</p>
        </div>
    <?php endif;?>

    </div>
</div>
<div class="clear-fix"></div>
<div class="mg-btm-30"></div>
<?php
Pjax::end();
?>
