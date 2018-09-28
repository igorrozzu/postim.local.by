<?php

use yii\helpers\Url;

?>

<div class="container-blackout-photo-popup"></div>
<div class="photo-popup">
    <div class="close-photo-popup"></div>
    <div class="photo-left-arrow">
        <div></div>
    </div>
    <div class="photo-popup-content">
        <div class="photo-info">
            <div class="photo-header">
                <?php if (isset($settings['post'])) : ?>
                    <a href="<?= Url::to([
                        'post/index',
                        'url' => $settings['post']->url_name,
                        'id' => $settings['post']->id,
                    ]) ?>">
                        <?= $settings['post']->data ?>
                    </a>
                <?php else: ?>
                    <a href=""></a>
                <?php endif; ?>
            </div>
        </div>
        <div class="photo-wrap">
            <div id="loader-box2">
                <div class="loader" style="border-left: 8px solid #FFFFFF;"></div>
            </div>
            <img class="photo-popup-item">
        </div>
    </div>
    <div class="photo-right-arrow">
        <div></div>
    </div>
    <ul class="wrap-photo-info">
        <li class="complain-gallery-text">Пожаловаться</li>
        <li class="photo-source" style="display: none;">
            <a href="#" rel="nofollow noopener" target="_blank"><span>Источник</span></a>
        </li>
    </ul>
    <div class="gallery-counter">
        <span id="start-photo-counter">1</span> из
        <span id="end-photo-counter"><?= $settings['photoCount'] ?? '' ?></span>
    </div>
</div>

<script>
	$(document).ready(function () {
		post.photos.resetContainer();

		main.initCustomScrollBar($('.photo-header'), {axis: "x", scrollInertia: 50, scrollbarPosition: "outside"});
		$(".photo-wrap").swipe({
			swipeRight: function (event, direction) {
				post.photos.prevPhoto();
			},
			swipeLeft: function (event, direction) {
				post.photos.nextPhoto();
			}
		});

		$('.photo-popup-item').load(function () {
			$('.photo-popup #loader-box2').css({display: 'none'});
			$('.photo-popup-item').css({display: 'block'});
		});
	});
</script>
