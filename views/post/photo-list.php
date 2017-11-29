<?php use yii\helpers\Url;

foreach ($dataProvider->getModels() as $photo):?>
    <div class="container-photo" style="background-image: url('<?=$photo->getPhotoPath()?>')" data-sequence="<?=$sequence++?>">
        <div class="block-blackout">
            <a href="<?=Url::to(['user/index', 'id' => $photo->user->id])?>">
                <img class="avatar-user" src="<?=$photo->user->getPhoto()?>">
            </a>
            <img class="origin-photo-feed" alt="<?=$photo->post->data??''?>" title="<?=$photo->post->data??''?>" src="<?=$photo->getPhotoPath()?>">
        </div>
    </div>
<?php endforeach;?>
<?php if ($hrefNext = $dataProvider->pagination->getLinks()['next'] ?? false): ?>
    <div class="btn-show-more"
         data-selector_replace="#btn-load-post-photos" id="btn-load-post-photos"
         data-href="<?=$hrefNext?>&loadTime=<?=$loadTime?>&sequence=<?=$sequence?>&hasTitle=true">
        <p>Показать больше фотографии</p></div>
<?php endif;?>