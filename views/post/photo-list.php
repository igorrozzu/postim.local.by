<?php foreach ($dataProvider->getModels() as $photo):?>
    <div class="container-photo" style="background-image: url('<?=$photo->getPhotoPath()?>')" data-sequence="<?=$sequence++?>">
        <div class="block-blackout">
            <img class="avatar-user" src="<?=$photo->user->getPhoto()?>">
        </div>
    </div>
<?php endforeach;?>
<?php if ($hrefNext = $dataProvider->pagination->getLinks()['next'] ?? false): ?>
    <div class="large-wide-button non-border fx-bottom btn-load-more"
         data-selector_replace="#btn-load-post-photos" id="btn-load-post-photos"
         data-href="<?=$hrefNext?>&loadTime=<?=$loadTime?>&sequence=<?=$sequence?>">
        <p>Показать больше фотографии</p></div>
<?php endif;?>