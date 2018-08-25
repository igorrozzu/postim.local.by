<?php

use app\widgets\cardsDiscounts\CardsDiscounts;
use yii\helpers\Url;
?>

<div class="block-content">
    <?php if ($dataProvider->getTotalCount() > 0 || $isShowDiscounts): ?>
        <h1 class="h2-c"><?=$post->data?> - скидки</h1>
    <?php endif; ?>

    <?php if ($isShowDiscounts): ?>
        <div class="cards-block-discount row-3">
            <a href="<?= Url::to(['discount/add', 'postId' => $post['id']]); ?>"
               class="large-wide-button non-border fx-bottom ">
                <p>Добавить скидку</p>
            </a>
        </div>
    <?php endif; ?>

    <?php if ($dataProvider->getTotalCount() > 0): ?>
        <div class="cards-block-discount row-3 main-pjax"
             data-favorites-state-url="/discount/favorite-state">
            <?= CardsDiscounts::widget([
                'dataprovider' => $dataProvider,
                'settings' => [
                    'show-more-btn' => true,
                    'replace-container-id' => 'feed-discounts',
                    'load-time' => time(),
                    'postId' => $post['id'],
                    'show-distance' => false,
                ]
            ]); ?>
        </div>
    <?php endif; ?>
</div>