<?php
use app\components\breadCrumb\BreadCrumb;
use \app\components\Helper;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

$category_name = $post->onlyOnceCategories[0]->name;
$this->title = $post->data.' - '.mb_strtolower(Yii::t('app/singular',$category_name))
    .' в '.Yii::t('app/locativus',$post->city->name).
    ', '.$post['address'].': фотографии';

$this->registerMetaTag([
    'name' => 'description',
    'content' => 'Фотогалерея, '.mb_strtolower(Yii::t('app/singular',$category_name)).
        ' '.$post->data.' в '.Yii::t('app/locativus',$post->city->name).
        ', '.$post['address'].'. Фотографии посетителей на Postim.by.'
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content'=> $post->data.' фотографии'
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
<?php
$js = <<<js
    $(document).ready(function() {
      map.setIdPlacesOnMap("$keyForMap");
    });
js;
echo "<script>$js</script>";
?>
<?php
Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => true,
    'id' => 'post-feeds',
    'linkSelector' => '#post-feeds .menu-btns-card a',
    'formSelector' => false,
])
?>
<div class="block-content">
    <?=BreadCrumb::widget(['breadcrumbParams'=>$breadcrumbParams])?>
    <h1 class="h1-v"><?=$post->data.' - фотографии'?></h1>
    <div class="block-info-reviewsAndfavorites" data-item-id="<?=$post->id?>" data-type="post">
        <div class="rating-b bg-r<?=$post['rating']?>" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
			<?=$post['rating']?>
            <meta itemprop="ratingCount" content="<?=$post->count_reviews?>">
            <meta itemprop="ratingValue" content="<?=$post['rating']?>">
            <meta itemprop="worstRating" content="1">
            <meta itemprop="bestRating" content="5">
        </div>
        <div class="count-reviews-text"><?=$post->count_reviews?> отзывов</div>
        <div class="add-favorite <?=$post['is_like']?'active':''?>"><?=$post->count_favorites?></div>
    </div>
</div>

<div class="block-flex-white">
    <div class="block-content">
        <div class="menu-btns-card">
            <a href="<?=Url::to(['post/index', 'url' => $post['url_name'], 'id' => $post['id']])?>" >
                <div class="btn2-menu"><span class="under-line">Информация</span></div>
            </a>
            <a href="<?=Url::to(['post/gallery', 'name' => $post['url_name'], 'postId' => $post['id']])?>">
                <div class="btn2-menu active"><span class="under-line">Фотографии <?=$photoCount?></span></div>
            </a>
            <?php if ($discountCount > 0 || isset($post->isCurrentUserOwner)):?>
                <a href="<?=Url::to(['post/get-discounts-by-post', 'name' => $post['url_name'], 'postId' => $post['id']])?>">
                    <div class="btn2-menu"><span class="under-line">Скидки <?=$discountCount?></span></div>
                </a>
            <?php endif;?>
        </div>
    </div>
</div>
<div class="block-content">
    <div class="block-photos-owner <?=!$ownerPhotos?'empty-photo':''?>" data-type="user">
        <?php foreach ($ownerPhotos as $index => $photo):?>
            <div class="container-photo" style="background-image: url('<?=$photo->getPhotoPath()?>')" data-sequence="<?=$index?>" data-source="<?=$photo->source ?? ''?>"
                 data-id="<?=$photo->id?>" data-status="<?=$photo->user_status?>">
                <div class="block-blackout">
                    <a href="<?=Url::to(['user/index', 'id' => $photo->user->id])?>">
                        <img class="avatar-user" src="<?=$photo->user->getPhoto()?>">
                    </a>
                    <img class="origin-photo-feed" alt="<?=$post->data?>" title="<?=$post->data?>" src="<?=$photo->getPhotoPath()?>">
                </div>
            </div>
        <?php endforeach;?>

        <label class="large-wide-button non-border fx-bottom photo-upload-sign" for="post-photos">
            <p>Добавить фотографии</p></label>
        <input type="file" name="post-photos" id="post-photos" style="display: none;" multiple
               accept="image/*,image/jpeg,image/gif,image/png" data-id="<?=$post->id?>">

    </div>
    <h2 class="h2-v">Фото посетителей</h2>
    <div class="block-photos-users" data-type="user">
        <?php echo $this->render('photo-list', [
            'dataProvider' => $dataProvider,
            'loadTime' => $loadTime,
            'sequence' => isset($index) ? $index + 1 : 0,
            'title' => $post->data
        ])?>
    </div>

</div>

<div class="container-blackout-photo-popup"></div>
<div class="photo-popup">
    <div class="close-photo-popup"></div>
    <div class="photo-left-arrow"><div></div></div>
    <div class="photo-popup-content">
        <div class="photo-info">
            <div class="photo-header" >
                <a href="<?=$post['url_name']?>-p<?=$post['id']?>"><?=$post->data?></a>
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
        <span id="end-photo-counter"><?=$photoCount?></span>
    </div>
</div>
<script>
    $(document).ready(function() {
        post.photos.setLoadTime(<?=$loadTime?>);
        post.photos.setPostId(<?=$post->id?>);
        post.photos.setAllPhotoCount(<?=$photoCount?>);
        post.photos.resetContainer();

        <?php if (isset($initPhotoSliderParams['photoId'])) :?>
            post.photos.initPhotoSlider({
                photoId: '<?=$initPhotoSliderParams['photoId']?>',
            });
        <?php endif;?>
        main.initCustomScrollBar($('.photo-header'),{axis: "x",scrollInertia: 50, scrollbarPosition: "outside"})
        $(".photo-wrap").swipe({
            swipeRight: function(event, direction) {
                post.photos.prevPhoto();
            },
            swipeLeft: function(event, direction) {
                post.photos.nextPhoto();
            }
        });
		menu_control.fireMethodClose();
    });
</script>
<?php
Pjax::end();
?>
