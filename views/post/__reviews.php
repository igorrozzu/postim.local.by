<div class="main_container_reviews">
	<h2 class="h2-c">Отзывы <span><?=$reviewsDataProvider->totalCount?></span></h2>

	<div class="block-write-reviews" data-post_id="<?=$post_id?>">
		<div class="profile-user-reviews">
			<img class="profile-icon60x60" src="<?=Yii::$app->user->getPhoto()?>">
			<?=Yii::$app->user->getName()?>
		</div>
		<div class="container-write-reviews">

		</div>
		<div class="large-wide-button open-container"><p>Написать новый отзыв</p></div>
	</div>

	<?php

		if ($reviewsDataProvider->totalCount) {
			echo \app\components\cardsReviewsWidget\CardsReviewsWidget::widget([
				'dataProvider' => $reviewsDataProvider,
				'settings'=>[
					'show-more-btn' => false,
                    'without_header'=>true
				]
			]);
			echo '<div class="review-show-more">
		            <div class="btn-show-more switch-reviews">Показать больше отзывов</div>
	              </div>';
		}
	?>

</div>
