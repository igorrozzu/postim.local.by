<?php
use app\components\cardsPlaceWidget\CardsPlaceWidget;
use app\components\cardsReviewsWidget\CardsReviewsWidget;
use yii\helpers\Url;
use yii\widgets\Pjax;
use \app\components\breadCrumb\BreadCrumb;
use \app\models\DescriptionPage;

$descriptionText = 'Отзывы и оценки посетителей различных мест в '.Yii::t('app/locativus',Yii::$app->city->getSelected_city()['name']).
    '. Оставь свое мнение на Postim.by. Рейтинг мест по популярности формируется по вашим отзывам.';

$descriptionPage = DescriptionPage::initMetaTags(function ()use ($descriptionText,$h1){
    $response = [
        'title' => 'Отзывы '.Yii::t('app/parental_slope',Yii::$app->city->getSelected_city()['name']).', сайт отзывов посетителей о местах - Postim.by',
        'description' => 'Отзывы и оценки посетителей различных мест в '.Yii::t('app/locativus',Yii::$app->city->getSelected_city()['name']).'. Оставь свое мнение на Postim.by.',
        'keywords' => 'Отзывы '.Yii::t('app/parental_slope',Yii::$app->city->getSelected_city()['name']),
        'descriptionText' => $descriptionText,
        'h1' => $h1,
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
<div class="block-content">
	<?= BreadCrumb::widget(['breadcrumbParams'=>$breadcrumbParams])?>
    <h1 class="h1-v"><?=$descriptionPage['h1']?></h1>
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
            <a href="/<?=Yii::$app->request->getPathInfo()?>">
                <div class="btn2-menu <?=($type === 'all') ? 'active' : ''?>">
                    <span class="under-line">Все</span>
                </div>
            </a>
            <a href="/<?=Yii::$app->request->getPathInfo().'?type=positive'?>">
                <div class="btn2-menu <?=($type === 'positive') ? 'active' : ''?>">
                    <span class="under-line">Положительные</span>
                </div>
            </a>
            <a href="/<?=Yii::$app->request->getPathInfo().'?type=negative'?>">
                <div class="btn2-menu <?=($type === 'negative') ? 'active' : ''?>">
                    <span class="under-line">Отрицательные</span>
                </div>
            </a>
        </div>
    </div>
</div>

<div class="block-content">
    <?php if($dataProvider->getTotalCount()):?>
        <div class="">
            <?= CardsReviewsWidget::widget([
                'dataProvider' => $dataProvider,
                'settings' => [
                    'show-more-btn' => true,
                    'replace-container-id' => 'feed-all-reviews',
                    'load-time' => $loadTime,
                ]
            ]); ?>
    <?php else:?>
        <div class="card-promo">
            <p class="card-text-notice">Отзывов не найдено</p>
        </div>
    <?php endif;?>

    </div>
</div>
<div class="clear-fix"></div>
<div class="mg-btm-30"></div>

<div class="container-blackout-photo-popup"></div>
<div class="photo-popup">
    <div class="close-photo-popup"></div>
    <div class="photo-left-arrow"><div></div></div>
    <div class="photo-popup-content">
        <div class="photo-info">
            <div class="photo-header" >
                <a href="#">12345</a>
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
        menu.openPageInLeftMenu($('#btn-all-reviews'));
    });
</script>

<?php
Pjax::end();
?>
<div class="block-content">
    <div class="description-text --description-width">
        <?=$descriptionPage['descriptionText']?>
    </div>
</div>