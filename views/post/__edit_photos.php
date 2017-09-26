<div class="block-inputs-gallery" style="display: none">
	<?php foreach ($photos as $photo):?>
		<div id="inputs_<?=md5($photo['link'])?>">
			<input class="src" name="photos[<?=$photo['link']?>][src]" type="text" value="<?=$photo->source?>">
			<input class="desc" name="photos[<?=$photo['link']?>][description]" type="text">
            <?php if(strpos($post['cover'],$photo['link'])!==false):?>
                <input class="confirm" name="photos[<?=$photo['link']?>][confirm]" type="text" value="true">
            <?php else:?>
                <input class="confirm" name="photos[<?=$photo['link']?>][confirm]" type="text">
            <?php endif;?>

		</div>
	<?php endforeach;?>
</div>
<div class="block-gallery">
	<?php foreach ($photos as $photo):?>
		<div id="<?=md5($photo['link'])?>" class="item-photo-from-gallery"
			 style="background-image: url('/post_photo/<?=$post->id?>/<?=$photo->link?>');">
			<div class="container-blackout">
				<div class="header-btns">
					<span class="btn-item-photo btn-close-photo-gallery"></span>
				</div>
				<div class="footer-btns">
					<?php if(strpos($post['cover'],$photo['link'])!==false):?>
                        <span class="btn-item-photo btn-confirm-photo-gallery active"></span>
					<?php else:?>
                        <span class="btn-item-photo btn-confirm-photo-gallery"></span>
					<?php endif;?>

					<span class="btn-item-photo btn-edit-photo-gallery"></span>
				</div>
			</div>
		</div>
	<?php endforeach;?>
</div>

