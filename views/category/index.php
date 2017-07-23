<?php
use \app\components\mainMenu\MainMenuWidget;
use \app\components\cardsPlaceWidget\CardsPlaceWidget;
use \app\components\cardsNewsWidget\CardsNewsWidget;
use \app\components\ListCityWidget\ListCityWidget;
use \yii\widgets\Pjax;
?>
<div class="margin-top60"></div>
<div id="map_block" class="block-map"></div>

<div class="block-content">
    <div class="bread-crumb">
        <a href="#">Главная</a>
        <span class="separator"></span>
        <a class="pre" href="#">Где поесть</a>
        <span class="separator"></span>
        <a href="#">Рестораны</a>
    </div>
    <h1 class="h1-v">Рестораны Беларуси</h1>
</div>
<?php
Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => true,
    'id' => 'feed-category',
    'linkSelector' => '#feed-category .block-sort a',
    'enableReplaceState'=>true,
    'formSelector' => false,
])
?>
<div class="block-flex-white">
    <div class="block-content">
        <div class="block-sort">
            <div class="btn-sort <?=$sort=='rating'?'active':''?>"><a href="<?=$url?>">По рейтингу</a></div>
            <div class="btn-sort <?=$sort=='new'?'active':''?>"><a href="<?=$url.'?sort=new'?>">Новые</a></div>
            <div class="btn-sort <?=$sort=='nigh'?'active':''?>"><a href="<?=$url.'?sort=nigh'?>">Рядом</a></div>
        </div>
    </div>
</div>
<div class="block-content">
    <div class="cards-block">
        <?= CardsPlaceWidget::widget(['dataprovider' => $dataProvider,'settings'=>['show-more-btn'=>true]]); ?>
    </div>
</div>
<?php

Pjax::end();

?>
