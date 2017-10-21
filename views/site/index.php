<?php
use \app\components\mainMenu\MainMenuWidget;
use \app\components\cardsPlaceWidget\CardsPlaceWidget;
use \app\components\cardsNewsWidget\CardsNewsWidget;
use \app\components\ListCityWidget\ListCityWidget;

?>

<div class="margin-top60"></div>
<div id="map_block" class="block-map preload-map">
    <div class="btns-map">
        <div class="action-map" title="Открыть карту"></div>
        <div class="find-me" title="Найти меня"></div>
        <div class="zoom-plus"></div>
        <div class="zoom-minus"></div>
    </div>
    <div id="map" style="display: none"></div>
</div>

<div class="block-content">
    <h1 class="h1-c center-mx">Карта лучших мест в <?=Yii::t('app/locativus',Yii::$app->city->getSelected_city()['name'])?> по отзывам посетителей</h1>
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
    <?php if($reviews->totalCount):?>
        <div class="clear-fix"></div>
        <h2 class="h2-c">Последние отзывы</h2>
        <div class="container-news">
            <div class="block-news">
                <?= \app\components\cardsReviewsWidget\CardsReviewsWidget::widget([
                    'dataProvider' => $reviews,
                    'settings'=>[
                        'show-more-btn' => false,
                    ]
                ]); ?>
                <div class="review-show-more main-pjax">
                    <a href="<?=Yii::$app->city->Selected_city['url_name']?'/'.Yii::$app->city->Selected_city['url_name']:''?>/otzyvy">
                        <div class="btn-show-more switch-all-reviews">Показать больше отзывов</div>
                    </a>
                </div>
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
          map.setIdPlacesOnMap("$keyForMap");
        });
js;

echo "<script>$js</script>";

?>
