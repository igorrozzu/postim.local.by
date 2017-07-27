<?php
use app\components\cardsReviewsWidget\CardsReviewsWidget;
use yii\helpers\Url;
use yii\widgets\Pjax;

?>
<div class="block-flex-white">
    <div class="block-content">
        <div class="menu-btns-card feeds-btn-bar">
            <a href="<?=Url::to(['user/reviews', 'id' => $user->id])?>">
                <div class="btn2-menu btn2-menu-active">Отзывы</div></a>
            <a href="<?=Url::to(['user/places', 'id' => $user->id])?>">
                <div class="btn2-menu">Места <?=$user->userInfo->count_places_added;?></div></a>
            <a href="<?=Url::to(['user/places', 'id' => $user->id, 'moderation' => 1])?>" >
                <div class="btn2-menu">На модерации <?=$user->userInfo->count_place_moderation;?></div></a>
        </div>
    </div>
</div>
<div class="block-content">
    <?= CardsReviewsWidget::widget([
        'dataProvider' => $dataProvider,
        'settings'=>[
            'show-more-btn' => true,
            'replace-container-id' => 'feed-reviews',
            'load-time' => $loadTime,
            'user-id' => $user->id,
        ]
    ]);
    ?>
</div>