<?php
use \app\components\mainMenu\MainMenuWidget;
use \app\components\cardsPlaceWidget\CardsPlaceWidget;
use \app\components\cardsNewsWidget\CardsNewsWidget;
use \app\components\ListCityWidget\ListCityWidget;
?>

<div class="margin-top60"></div>
<div id="map_block" class="block-map"></div>

<div class="block-content">
    <h1 class="h1-c center-mx">Сервис поиска и добовления интересных мест,
        карта достопримечательностей Беларуси</h1>
    <?=MainMenuWidget::widget(['typeMenu'=>MainMenuWidget::$catalogMenu])?>
    <?php if($spotlight->totalCount):?>
    <h2 class="h2-c">В центре внимания</h2>
    <div class="cards-block">
        <?=CardsPlaceWidget::widget(['dataprovider'=>$spotlight,'settings'=>['show-more-btn'=>false]])?>
    </div>
    <div class="clear-fix"></div>
    <?php endif;?>
    <h2 class="h2-c">Последние новости</h2>
    <div class="container-news">
        <?=CardsNewsWidget::widget(['dataprovider'=>$news])?>
    </div>

</div>
<div class="clear-fix"></div>
<?=ListCityWidget::widget(['settings'=>
    [
        'id'=>'content_list_city',
        'is_menu'=>false
    ]
]);?>