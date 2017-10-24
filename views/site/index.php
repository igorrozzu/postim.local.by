<?php
use app\components\cardsReviewsWidget\CardsReviewsWidget;
use \app\components\mainMenu\MainMenuWidget;
use \app\components\cardsPlaceWidget\CardsPlaceWidget;
use \app\components\cardsNewsWidget\CardsNewsWidget;
use \app\components\ListCityWidget\ListCityWidget;


$this->title = "Карта ".Yii::t('app/parental_slope',Yii::$app->city->getSelected_city()['name'])." — лучшие места по отзывам посетителей - Postim.by";
$this->registerMetaTag([
    'name' => 'description',
    'content' => 'Подробная карта '.Yii::t('app/parental_slope',Yii::$app->city->getSelected_city()['name']).
        ', лучшие места по отзывам посетителей. Найдено '.$spotlight->totalCount.' — удобный поиск на карте Postim.by!'
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content'=> 'карта '.Yii::t('app/parental_slope',Yii::$app->city->getSelected_city()['name'])
]);

$descriptionText = 'Подробная карта '.Yii::t('app/parental_slope',Yii::$app->city->getSelected_city()['name'])
    .', лучшие места по отзывам посетителей. Найдено '.$spotlight->totalCount
    .' — удобный поиск на карте Postim.by!</br> Время работы и прочую информацию смотрите у нас на сайте.';

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
                <?= CardsReviewsWidget::widget([
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
<?= ListCityWidget::widget(['settings' => [
        'id' => 'content_list_city',
        'is_menu' => false
]]);?>

<div class="block-content">
    <div class="description-text">
        <?=$descriptionText?>
    </div>
</div>

<div class="container-blackout-photo-popup"></div>
<div class="photo-popup">
    <div class="close-photo-popup"></div>
    <div class="photo-left-arrow"><div></div></div>
    <div class="photo-popup-content">
        <div class="photo-info">
            <div class="photo-header">
                <a href=""></a>
            </div>
        </div>
        <div class="photo-wrap">
            <img class="photo-popup-item">
        </div>
    </div>
    <div class="photo-right-arrow"><div></div></div>
    <ul class="wrap-photo-info">
        <li class="complain-gallery-text">Пожаловаться</li>
        <li class="photo-source" style="display: none;">
            <a href="#" target="_blank"><span>Источник</span></a>
        </li>
    </ul>
    <div class="gallery-counter">
        <span id="start-photo-counter">1</span> из
        <span id="end-photo-counter"></span>
    </div>
</div>

<script>
    $(document).ready(function() {
        post.photos.resetContainer();
        post.photos.isChangeTitleInSlider(true);
        <?php if (isset($initPhotoSliderParams['photoId'])) :?>
            post.photos.initPhotoSlider({
                photoId: '<?=$initPhotoSliderParams['photoId']?>',
                reviewId: <?=$initPhotoSliderParams['reviewId']?>,
                type: 'review'
            });
        <?php endif;?>
        $('.photo-header').mCustomScrollbar({axis: "x",scrollInertia: 50, scrollbarPosition: "outside"});
        $(".photo-wrap").swipe({
            swipeRight: function(event, direction) {
                post.photos.prevPhoto();
            },
            swipeLeft: function(event, direction) {
                post.photos.nextPhoto();
            }
        });
        menu_control.fireMethodClose();
        map.setIdPlacesOnMap("<?=$keyForMap?>");
    });
</script>
