<?php
use app\components\cardsReviewsWidget\CardsReviewsWidget;
use \app\components\mainMenu\MainMenuWidget;
use \app\components\cardsPlaceWidget\CardsPlaceWidget;
use \app\components\cardsNewsWidget\CardsNewsWidget;
use \app\models\DescriptionPage;


$descriptionPage = DescriptionPage::initMetaTags(function ()use ($spotlight){
    $response = [
        'title' => "Карта ".Yii::t('app/parental_slope',Yii::$app->city->getSelected_city()['name'])." — лучшие места по отзывам посетителей - Postim.by",

        'description' => 'Подробная карта '.Yii::t('app/parental_slope',Yii::$app->city->getSelected_city()['name']).
        ', лучшие места по отзывам посетителей — удобный поиск на карте Postim.by!',

        'keywords' => 'карта '.Yii::t('app/parental_slope',Yii::$app->city->getSelected_city()['name']),

        'descriptionText' => 'Подробная карта '.Yii::t('app/parental_slope',Yii::$app->city->getSelected_city()['name'])
            .', лучшие места по отзывам посетителей — удобный поиск на карте Postim.by!</br> Время работы и прочую информацию смотрите у нас на сайте.',

        'h1' => 'Рекомендательный сервис лучших мест в '.Yii::t('app/locativus',Yii::$app->city->getSelected_city()['name']).' по отзывам посетителей',
    ];

    return $response;
});


$this->title = $descriptionPage['title'];
$this->registerMetaTag([
    'name' => 'description',
    'content' => $descriptionPage['description']
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content' => $descriptionPage['keywords']
]);

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
    <h1 class="h1-c center-mx"><?=$descriptionPage['h1']?></h1>
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
<div class="block-content">

    <div class="container-columns">
        <div class="__first-column">

            <?php if($reviews->totalCount):?>
                <div class="clear-fix"></div>
                <h2 class="h2-c">Новые отзывы</h2>
                <div class="container-news">
                    <div class="block-news block-new-reviews">
                        <?= CardsReviewsWidget::widget([
                            'dataProvider' => $reviews,
                            'settings'=>[
                                'show-more-btn' => false,
                                'noIndexData' => true
                            ]
                        ]); ?>
                        <noindex>
                            <div class="review-show-more main-pjax">
                                <a href="<?= Yii::$app->city->Selected_city['url_name'] ? '/' . Yii::$app->city->Selected_city['url_name'] : '' ?>/otzyvy">
                                    <div class="btn-show-more switch-all-reviews">Показать больше отзывов</div>
                                </a>
                            </div>
                        </noindex>
                    </div>
                </div>
            <?php endif;?>

        </div>
        <div class="__second-column">
            <div class="--100px-30px">
                <?= \app\components\rightBlock\RightBlockWidget::widget()?>
            </div>

        </div>
    </div>

</div>
<div class="clear-fix"></div>

<div class="block-content" style="margin-top: 30px;">
    <div class="description-text --description-width">
        <?=$descriptionPage['descriptionText']?>
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
            <a href="#" rel="nofollow noopener" target="_blank"><span>Источник</span></a>
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
