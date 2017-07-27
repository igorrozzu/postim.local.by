<?php
use app\components\cardsPlaceWidget\CardsPlaceWidget;
use yii\helpers\Url;

?>

<div class="block-flex-white">
    <div class="block-content">
        <div class="menu-btns-card feeds-btn-bar">
            <a href="<?=Url::to(['user/reviews', 'id' => $user->id])?>">
                <div class="btn2-menu">Отзывы</div></a>
            <a href="<?=Url::to(['user/places', 'id' => $user->id])?>">
                <div class="btn2-menu <?php if($moderation === null) echo 'btn2-menu-active'?>">
                    Места <?=$user->userInfo->count_places_added;?></div></a>
            <a href="<?=Url::to(['user/places', 'id' => $user->id, 'moderation' => 1])?>" >
                <div class="btn2-menu <?php if($moderation !== null) echo 'btn2-menu-active'?>">
                    На модерации <?=$user->userInfo->count_place_moderation;?></div></a>
        </div>
    </div>
</div>
<div class="block-content">
    <div class="cards-block">
    <?= CardsPlaceWidget::widget([
        'dataprovider' => $dataProvider,
        'settings'=>[
            'show-more-btn' => true,
            'replace-container-id' => 'feed-posts',
            'load-time' => $loadTime,
        ]
    ]);
    ?>
    </div>
</div>