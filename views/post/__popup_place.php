<div class="container-place-popup">
    <div class="header-place-popup">
        <div class="main-pjax">
            <a href="/<?= $post['url_name'] ?>-p<?= $post['id'] ?>"><?= $post->data ?></a>
        </div>
    </div>
    <div class="btns-place-popup">
        <div class="block-info-reviewsAndfavorites" data-item-id="<?= $post['id'] ?>" data-type="post">
            <div class="rating-b bg-r<?= $post['rating'] ?>"><?= $post['rating'] ?></div>
            <div class="count-reviews-text">
                <?= $post['count_reviews'] ?>
                <?= Yii::$app->formatter->getNumEnding($post['count_reviews'], [
                    'отзыв',
                    'отзыва',
                    'отзывов',
                ]) ?>
            </div>
            <div class="add-favorite <?= $post['is_like'] ? 'active' : '' ?>"><?= $post['count_favorites'] ?></div>
        </div>
    </div>
    <?php if ($post->is_open): ?>
        <div style="margin-top: 12px" class="open"><span>Открыто <?= $post->timeOpenOrClosed ?></span></div>
    <?php else: ?>
        <div style="margin-top: 12px" class="close"><span>Закрыто <?= $post->timeOpenOrClosed ?></span></div>
    <?php endif; ?>
    <div class="address-popup"><img class="icon-address" src="/img/icon-address-info.png">
        <span><?= $post['address'] ?></span></div>
</div>