<?php
use app\components\cardsPlaceWidget\CardsPlaceWidget;
use yii\helpers\Url;

?>

<div class="block-flex-white">
    <div class="block-content">
        <div class="menu-btns-card feeds-btn-bar">
            <a href="<?=Url::to(['user/reviews', 'id' => $user->id])?>">
                <div class="btn2-menu"><span class="under-line">Отзывы <?=$user->userInfo->count_reviews?$user->userInfo->count_reviews:'';?></span></div></a>
            <a href="<?=Url::to(['user/places', 'id' => $user->id])?>">
                <div class="btn2-menu <?php if($moderation === null) echo 'active'?>">
                    <span class="under-line">Места <?=$user->userInfo->count_places_added?$user->userInfo->count_places_added:'';?></span></div></a>
            <?php if($user->id == Yii::$app->user->getId() && Yii::$app->user->identity->role <= 1):?>
                <a href="<?=Url::to(['user/places', 'id' => $user->id, 'moderation' => 1])?>" >
                    <div class="btn2-menu <?php if($moderation !== null) echo 'active'?>">
                        <span class="under-line">На модерации <?=$user->userInfo->count_place_moderation?$user->userInfo->count_place_moderation:'';?></span></div></a>
            <?php endif;?>
        </div>
    </div>
</div>
<div class="block-content">

    <?php
        if ($dataProvider->totalCount) {
            echo '<div class="cards-block">';
            echo CardsPlaceWidget::widget([
                'dataprovider' => $dataProvider,
                'settings' => [
                    'show-more-btn' => true,
                    'replace-container-id' => 'feed-posts',
                    'load-time' => $loadTime,
                    'moderation' => $moderation === null ? false : true,
                ],
            ]);
            echo '</div>';
        }
    ?>

	<?php if($moderation !== null && $user->userInfo->count_place_moderation == 0):?>
        <div style="margin-top: 10px; display: flex"></div>
        <div class="card-promo">
            <p class="card-text-notice">У вас нет на модерации ни одного места</p>
        </div>
    <?php elseif ($moderation === null && $user->userInfo->count_places_added== 0):?>
        <div style="margin-top: 10px; display: flex"></div>
        <div class="card-promo">
            <?php if($user->id == Yii::$app->user->getId()):?>
                <p class="card-text-notice">Вы не добавляли и не редактировали места</p>
            <?php else:?>
                <p class="card-text-notice">Пользователь не добавлял и не редактировал места</p>
            <?php endif;?>
        </div>
	<?php endif;?>
</div>