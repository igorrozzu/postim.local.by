<?php

use \yii\helpers\Url;

?>

<div class="main_container_reviews">
    <h2 class="h2-c">Отзывы <span><?= $reviewsDataProvider->totalCount ?></span></h2>
    <div class="block-write-reviews main-write" data-post_id="<?= $post->id ?>">
        <div class="profile-user-reviews">
            <img class="profile-icon60x60" src="<?= Yii::$app->user->getPhoto() ?>">
            <?= Yii::$app->user->getName() ?>
        </div>
        <div class="container-write-reviews"></div>
        <div class="large-wide-button open-container"><p>Написать новый отзыв</p></div>
    </div>
    <?php if ($reviewsDataProvider->totalCount || Yii::$app->request->get('type', false)): ?>
        <div class="block-flex-white inside" style="margin-top: 30px">
            <div class="block-content">
                <div class="menu-btns-card feeds-btn-bar">
                    <a href="<?= Url::to(['post/index', 'url' => $post['url_name'], 'id' => $post['id']]) ?>">
                        <div class="btn2-menu <?= ($type === 'all') ? 'active' : '' ?>">
                            <span class="under-line">Все</span>
                        </div>
                    </a>
                    <a href="<?= Url::to([
                        'post/index',
                        'type' => 'positive',
                        'url' => $post['url_name'],
                        'id' => $post['id'],
                    ]) ?>">
                        <div class="btn2-menu <?= ($type === 'positive') ? 'active' : '' ?>">
                            <span class="under-line">Положительные</span>
                        </div>
                    </a>
                    <a href="<?= Url::to([
                        'post/index',
                        'type' => 'negative',
                        'url' => $post['url_name'],
                        'id' => $post['id'],
                    ]) ?>">
                        <div class="btn2-menu <?= ($type === 'negative') ? 'active' : '' ?>">
                            <span class="under-line">Отрицательные</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <?= \app\components\cardsReviewsWidget\CardsReviewsWidget::widget([
            'dataProvider' => $reviewsDataProvider,
            'settings' => [
                'show-more-btn' => true,
                'replace-container-id' => 'feed-reviews',
                'load-time' => $loadTime,
                'without_header' => true,
                'btn_sort' => true,
            ],
        ]); ?>
    <?php endif; ?>
    <div class="margin-top60"></div>
</div>

