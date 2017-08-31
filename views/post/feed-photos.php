<?php
use app\components\breadCrumb\BreadCrumb;
use \app\components\Helper;
use yii\helpers\Url;
use yii\widgets\Pjax;
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
    'linkSelector' => '.menu-btns-card a',
    'formSelector' => false,
])
?>
<div class="block-content">
    <?=BreadCrumb::widget(['breadcrumbParams'=>$breadcrumbParams])?>
    <h1 class="h1-v"><?=$post->data?></h1>
    <div class="block-info-reviewsAndfavorites" data-item-id="<?=$post->id?>" data-type="post">
        <div class="rating-b bg-r<?=$post['rating']?>"><?=$post['rating']?></div>
        <div class="count-reviews-text"><?=$post->count_reviews?> отзывов</div>
        <div class="add-favorite <?=$post['is_like']?'active':''?>"><?=$post->count_favorites?></div>
    </div>
</div>

<div class="block-flex-white">
    <div class="block-content">
        <div class="menu-btns-card">
            <a href="<?=Url::to(['post/index', 'url' => $post['url_name'], 'id' => $post['id']])?>" >
                <div class="btn2-menu">Информация</div>
            </a>
            <a href="<?=Url::to(['post/gallery', 'name' => $post['url_name'], 'postId' => $post['id']])?>">
                <div class="btn2-menu active">Фотографии <?=$photoCount?></div>
            </a>
            <a href="">
                <div class="btn2-menu">Отзывы</div>
            </a>
            <a href="">
                <div class="btn2-menu">Скидки</div>
            </a>
        </div>
    </div>
</div>
<div class="block-content">
    <div class="block-photos-owner" data-type="owner">
        <?php foreach ($ownerPhotos as $index => $photo):?>
            <div class="container-photo" style="background-image: url('<?=$photo->getPhotoPath()?>')" data-sequence="<?=$index?>" data-source="<?=$photo->source ?? ''?>">
                <div class="block-blackout">
                    <a href="<?=Url::to(['user/index', 'id' => $photo->user->id])?>">
                        <img class="avatar-user" src="<?=$photo->user->getPhoto()?>">
                    </a>
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
            'sequence' => 0,
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
            <div class="pre-photo pre-popup-photo"></div>
            <img class="photo-popup-item">
            <div class="next-photo next-popup-photo"></div>
        </div>
        <div class="photo-source" style="display: none;">
            <a href="#" target="_blank">Источник</a>
        </div>
    </div>
    <div class="photo-right-arrow"><div></div></div>
</div>
<script>
    $(document).ready(function() {
        post.photos.setLoadTime(<?=$loadTime?>);
        post.photos.setPostId(<?=$post->id?>);
    });
</script>

<?php
Pjax::end();
?>
