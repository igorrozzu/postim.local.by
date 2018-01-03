<?php

use app\components\breadCrumb\BreadCrumb;
use app\components\cardsPromoWidget\CardsPromoWidget;
use yii\helpers\Url;
use yii\widgets\Pjax;

$this->title = 'Мои промокоды на Postim.by';
?>
<div class="margin-top60"></div>
<div class="block-content">
    <?= BreadCrumb::widget(['breadcrumbParams'=>$breadcrumbParams])?>
    <h1 class="h1-v">Мои промокоды</h1>
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
            <a href="<?=Url::to(['user/get-promocodes'])?>">
                <div class="btn2-menu <?=($status === 'active') ? 'active' : ''?>">
                    Действующие
                </div>
            </a>
            <a href="<?=Url::to(['user/get-promocodes', 'status' => 'unactive', 'type' => 'promocode'])?>">
                <div class="btn2-menu <?=($status === 'unactive') ? 'active' : ''?>">
                    Использованые
                </div>
            </a>
            <a href="<?=Url::to(['user/get-promocodes', 'status' => 'all', 'type' => 'promocode'])?>" >
                <div class="btn2-menu <?=($status === 'all') ? 'active' : ''?>">
                    Все
                </div>
            </a>
        </div>
    </div>
</div>

<div class="block-content">
    <div class="container-cards-promo">
    <?= CardsPromoWidget::widget([
        'dataProvider' => $dataProvider,
        'settings' => [
            'show-more-btn' => true,
            'replace-container-id' => 'feed-promo',
            'load-time' => $loadTime,
            'show-more-btn-text' => 'Показать больше промокодов',
            'not-found-text' => 'Вы пока не купили ни одного промокода.',
        ]
    ]);?>
    </div>
</div>
<?php
Pjax::end();
?>
<div style="margin-bottom:30px;"></div>
