<?php
use app\components\cardsReviewsWidget\CardsReviewsWidget;
use yii\helpers\Url;
use yii\widgets\Pjax;

?>
<div class="block-flex-white">
    <div class="block-content">
        <div class="menu-btns-card feeds-btn-bar">
            <a href="<?=Url::to(['user/reviews', 'id' => $user->id])?>">
                <div class="btn2-menu active"><span class="under-line">Отзывы <?=$user->userInfo->count_reviews?$user->userInfo->count_reviews:'';?></span></div></a>
            <a href="<?=Url::to(['user/places', 'id' => $user->id])?>">
                <div class="btn2-menu"><span class="under-line">Места <?=$user->userInfo->count_places_added?$user->userInfo->count_places_added:'';?></span></div></a>
			<?php if($user->id == Yii::$app->user->getId() && Yii::$app->user->identity->role <= 1):?>
                <a href="<?=Url::to(['user/places', 'id' => $user->id, 'moderation' => 1])?>" >
                    <div class="btn2-menu">
                        <span class="under-line">На модерации <?=$user->userInfo->count_place_moderation?$user->userInfo->count_place_moderation:'';?></span></div></a>
			<?php endif;?>
        </div>
    </div>
</div>
<div class="block-content">
    <?php if($dataProvider->totalCount):?>
    <?= CardsReviewsWidget::widget([
        'dataProvider' => $dataProvider,
        'settings'=>[
            'show-more-btn' => true,
            'replace-container-id' => 'feed-reviews',
            'load-time' => $loadTime,
        ]
    ]);
    ?>
    <?php else:?>
        <div style="margin-top: 10px; display: flex"></div>
    <div class="card-promo">
		<?php if($user->id == Yii::$app->user->getId()):?>
            <p class="card-text-notice">Вы не написали ни одного отзыва</p>
		<?php else:?>
            <p class="card-text-notice">Пользователь не написал ни одного отзыва</p>
		<?php endif;?>
    </div>
    <?php endif;?>
</div>