<?php
use \app\components\mainMenu\MainMenuWidget;
use \app\components\cardsPlaceWidget\CardsPlaceWidget;
use \app\components\cardsNewsWidget\CardsNewsWidget;
use \app\components\ListCityWidget\ListCityWidget;
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
<div class="block-flex-white">
    <div class="block-content">
        <div class="block-sort">
            <div class="btn-sort">По рейтингу</div>
            <div class="btn-sort">Новые</div>
            <div class="btn-sort">Рядом</div>
        </div>
    </div>
</div>
<div class="block-content">
    <div class="cards-block">
        <?= CardsPlaceWidget::widget(['dataprovider' => $posts,'settings'=>['show-more-btn'=>true]]); ?>
    </div>
</div>