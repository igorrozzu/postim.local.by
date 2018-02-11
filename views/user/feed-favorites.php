<?php

use app\components\breadCrumb\BreadCrumb;
use yii\helpers\Url;
use yii\widgets\Pjax;
$this->title = 'Избранное на Postim.by';
?>
<div class="margin-top60"></div>
<div class="block-content">
    <?= BreadCrumb::widget(['breadcrumbParams'=>$breadcrumbParams])?>
    <h1 class="h1-v">Избранное</h1>
</div>
<?php
Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => false,
    'id' => 'feeds-of-user',
    'linkSelector' => '#feeds-of-user .feeds-btn-bar a',
    'formSelector' => false,
])
?>
<div class="block-flex-white">
    <div class="block-content">
        <div class="menu-btns-card feeds-btn-bar">
            <a href="<?=Url::to(['user/izbrannoe'])?>">
                <div class="btn2-menu <?= $feed === 'posts' ? 'active' : ''?>">
                    <span class="under-line">Места</span>
                </div>
            </a>
            <a href="<?=Url::to(['user/izbrannoe', 'favorite' => 'news'])?>">
                <div class="btn2-menu <?= $feed === 'news' ? 'active' : ''?>">
                    <span class="under-line">Новости</span>
                </div>
            </a>
            <a href="<?=Url::to(['user/izbrannoe', 'favorite' => 'discounts'])?>">
                <div class="btn2-menu <?= $feed === 'discounts' ? 'active' : ''?>">
                    <span class="under-line">Скидки</span>
                </div>
            </a>
        </div>
    </div>
</div>

<div class="block-content">
    <?php if($dataProvider->getTotalCount()):?>
        <div <?=$widgetWrapAttributes?>>
            <?= $widgetClassName::widget([
                'dataprovider' => $dataProvider,
                'settings' => [
                    'show-more-btn' => true,
                    'replace-container-id' => 'feed-favorites',
                    'load-time' => $loadTime,
                ]
            ]); ?>
        </div>
    <?php else:?>
        <div style="margin-top: 10px; display: flex"></div>
            <div class="container-message">
                <div class="message-filter">
                    <div class="icon-favorite"></div>
                    <p>Вы пока ничего не добавили в Избранное</p>
                    <span>Добавляйте в Избранное, нажав на значок «сердечко», чтобы не потерять!</span>
                </div>
            </div>
    <?php endif;?>
</div>
<div class="clear-fix"></div>
<div class="mg-btm-30"></div>
<?php
Pjax::end();
?>
