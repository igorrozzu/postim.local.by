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
    'enablePushState' => false,
    'id' => 'feed-category',
    'linkSelector' => '#feed-category .block-sort a',
    'formSelector' => false,
])
?>
<div class="block-flex-white">
    <div class="block-content">
        <div class="block-sort">
            <a href="<?=$url?>" class="btn-sort <?=$sort=='rating'?'active':''?>">По рейтингу</a>
            <a href="<?=$url.'?sort=new'?>" class="btn-sort <?=$sort=='new'?'active':''?>">Новые</a>
            <a href="<?=$url.'?sort=nigh'?>" class="btn-sort <?=$sort=='nigh'?'active':''?>">Рядом</a>
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
