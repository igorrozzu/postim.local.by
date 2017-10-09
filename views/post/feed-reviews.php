<?php
use app\components\breadCrumb\BreadCrumb;
use \app\components\Helper;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;
?>
<div class="margin-top60"></div>
<div id="map_block" class="block-map preload-map">
    <div class="btns-map">
        <div class="action-map" title="Открыть карту"></div>
        <div class="find-me" title="Найти меня"></div>
        <div class="zoom-plus"></div>
        <div class="zoom-minus"></div>
    </div>

    <div id="map" style="display: none"></div>
</div>

<?php
$js = <<<js
    $(document).ready(function() {
      map.setIdPlacesOnMap("$keyForMap");
    });
js;
echo "<script>$js</script>";
?>
<?php
Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => true,
    'id' => 'post-feeds',
    'linkSelector' => '#post-feeds .menu-btns-card a',
    'formSelector' => false,
])
?>
<div class="block-content">
    <?=BreadCrumb::widget(['breadcrumbParams'=>$breadcrumbParams])?>
    <h1 class="h1-v"><?=$post->data?></h1>
    <div class="block-info-reviewsAndfavorites" data-item-id="<?=$post->id?>" data-type="post">
        <div class="rating-b bg-r<?=$post['rating']?>"><?=$post['rating']?></div>
        <div class="count-reviews-text"><?=$post->count_reviews?> отзывов</div>
        <div class="add-favorite <?=$post['is_like']?'active':''?>"><?=$post->count_favorites?></div>
    </div>
</div>

<div class="block-flex-white">
    <div class="block-content">
        <div class="menu-btns-card">
            <a href="<?=Url::to(['post/index', 'url' => $post['url_name'], 'id' => $post['id']])?>" >
                <div class="btn2-menu">Информация</div>
            </a>
            <a href="<?=Url::to(['post/gallery', 'name' => $post['url_name'], 'postId' => $post['id']])?>">
                <div class="btn2-menu">Фотографии <?=$photoCount?></div>
            </a>
            <a href="<?=Url::to(['post/reviews', 'name' => $post['url_name'], 'postId' => $post['id']])?>">
                <div class="btn2-menu active">Отзывы <?=$post['count_reviews']?></div>
            </a>
        </div>
    </div>
</div>
<div class="block-content">

    <div class="main_container_reviews">

        <div class="block-write-reviews" data-post_id="<?=$post->id?>">
            <div class="profile-user-reviews">
                <img class="profile-icon60x60" src="<?=Yii::$app->user->getPhoto()?>">
				<?=Yii::$app->user->getName()?>
            </div>
            <div class="container-write-reviews"></div>
            <div class="large-wide-button open-container"><p>Написать новый отзыв</p></div>
        </div>
        <div class="block-sort-reviews menu-btns-card">
            <a href="<?=Url::to(['post/reviews','name' => $post['url_name'], 'postId' => $post['id']])?>">
                <div class="btn-sort-reviews <?=$type=='all'?'active':''?>">Все отзывы</div>
            </a>
            <a href="<?=Url::to(['post/reviews', 'type' => 'positive','name' => $post['url_name'], 'postId' => $post['id']])?>">
                <div class="btn-sort-reviews <?=$type=='positive'?'active':''?>" >Положительные</div>
            </a>
            <a href="<?=Url::to(['post/reviews', 'type' => 'negative','name' => $post['url_name'], 'postId' => $post['id']])?>">
                <div class="btn-sort-reviews <?=$type=='negative'?'active':''?>" >Отрицательные</div>
            </a>
        </div>

		<?php

			if ($reviewsDataProvider->totalCount) {
				echo \app\components\cardsReviewsWidget\CardsReviewsWidget::widget([
					'dataProvider' => $reviewsDataProvider,
					'settings'=>[
						'show-more-btn' => true,
						'replace-container-id' => 'feed-reviews',
						'load-time' => $loadTime,
						'without_header'=>true,
                        'btn_sort'=>true
					]
				]);
			}
		?>

    </div>
</div>
<div style="margin-top: 30px"></div>


<?php
Pjax::end();
?>
