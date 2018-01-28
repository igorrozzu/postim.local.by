<?php
use app\components\breadCrumb\BreadCrumb;
use \app\components\Helper;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

$category_name = $post->onlyOnceCategories[0]->name;
$this->title = $post->data . ': фото ' . $photo->id;

$this->registerMetaTag([
    'name' => 'description',
    'content' => $post->data . ' - фото ' . $photo->id . ': больше фотографий смотрите на сайте Postim.by'
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content'=> $post->data . ', фото, ' . 'фотографии, фотогаллерея'
]);
?>
<div class="margin-top60"></div>
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
    <h1 class="h1-v"><?=$post->data.' - фото'?></h1>
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
                <div class="btn2-menu"><span class="under-line">Фотографии <?=$photoCount?></span></div>
            </a>
        </div>
    </div>
</div>
<div class="block-content">

    <div class="container-post">
        <div class="block-photo-post">
            <img src="<?=$photo->getPhotoPath()?>" alt="<?=$post->data ?? ''?>" title="<?=$post->data ?? ''?>">
        </div>
        <?php if($photo->source):?>
        <div class="photo-desc">
            <a href="<?=$photo->source?>" rel="nofollow noindex">Источник</a>
        </div>
        <?php endif;?>
    </div>

</div>
<script>
    $(document).ready(function() {
		menu_control.fireMethodClose();
    });
</script>
<?php
Pjax::end();
?>
