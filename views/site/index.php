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
    <?= MainMenuWidget::widget(['typeMenu' => MainMenuWidget::$catalogMenu]) ?>
    <?php if ($spotlight->totalCount): ?>
        <h2 class="h2-c">В центре внимания</h2>
        <div class="cards-block">
            <?= CardsPlaceWidget::widget(['dataprovider' => $spotlight, 'settings' => ['show-more-btn' => false]]) ?>
        </div>
        <div class="clear-fix"></div>
    <?php endif; ?>
    <?php if($news->totalCount):?>
    <h2 class="h2-c">Последние новости</h2>
    <div class="container-news">
        <div class="block-news">
            <?= CardsNewsWidget::widget(['dataprovider' => $news, 'settings' => ['last-news' => true]]) ?>
        </div>
    </div>
    <?php endif;?>

</div>
<div class="clear-fix"></div>
<?= ListCityWidget::widget(['settings' =>
    [
        'id' => 'content_list_city',
        'is_menu' => false
    ]
]); ?>

<?php

$js = <<<js
        $(document).ready(function() {
          menu_control.fireMethodClose();
        });
js;

echo "<script>$js</script>";

?>
