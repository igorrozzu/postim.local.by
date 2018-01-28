<?php use yii\helpers\Url;

foreach ($dataProvider->getModels() as $photo):?>
    <div class="block-li-photo">
        <a href="<?=Url::to(['post/photo', 'name' => $photo->post->url_name, 'idPhoto' => $photo->id])?>" title="<?=$photo->post->data ?? ''?>">
            <div class="container-photo" style="background-image: url('<?=$photo->getPhotoPath()?>')" data-sequence="<?=$sequence++?>">
                <div class="block-blackout"></div>
            </div>
        </a>
        <div class="block-author">
            <a href="<?=Url::to(['user/index', 'id' => $photo->user->id])?>">
                <img class="avatar-user" src="<?=$photo->user->getPhoto()?>">
            </a>
        </div>
    </div>
<?php endforeach;?>
<?php if ($hrefNext = $dataProvider->pagination->getLinks()['next'] ?? false): ?>
    <div class="btn-show-more"
         data-selector_replace="#btn-load-post-photos" id="btn-load-post-photos"
         data-href="<?=$hrefNext?>&loadTime=<?=$loadTime?>&sequence=<?=$sequence?>&hasTitle=true">
        <p>Показать больше фотографии</p></div>
<?php endif;?>