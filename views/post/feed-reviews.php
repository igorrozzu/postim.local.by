<?php
use app\components\breadCrumb\BreadCrumb;
use \app\components\Helper;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

$category_name = $post->onlyOnceCategories[0]->name;
$this->title = $post->data.' - '.mb_strtolower(Yii::t('app/singular',$category_name))
    .' в '.Yii::t('app/locativus',$post->city->name).
    ', '.$post['address'].': отзывы посетителей';

$this->registerMetaTag([
    'name' => 'description',
    'content' => 'Отзывы и оценки посетителей на '.mb_strtolower(Yii::t('app/singular',$category_name)).
        ' '.$post->data.' в '.Yii::t('app/locativus',$post->city->name).
        ', '.$post['address'].'. Оставь свое мнение на Postim.by.'
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content'=> $post->data.' отзывы'
]);

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
    <h1 class="h1-v"><?=$post->data.' - отзывы'?></h1>
    <div class="block-info-reviewsAndfavorites" data-item-id="<?=$post->id?>" data-type="post">
        <div class="rating-b bg-r<?=$post['rating']?>" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
			<?=$post['rating']?>
            <meta itemprop="ratingCount" content="<?=$post->count_reviews?>">
            <meta itemprop="ratingValue" content="<?=$post['rating']?>">
            <meta itemprop="worstRating" content="1">
            <meta itemprop="bestRating" content="5">
        </div>
        <div class="count-reviews-text"><?=$post->count_reviews?> отзывов</div>
        <div class="add-favorite <?=$post['is_like']?'active':''?>"><?=$post->count_favorites?></div>
    </div>
</div>

<div class="block-flex-white">
    <div class="block-content">
        <div class="menu-btns-card">
            <a href="<?=Url::to(['post/index', 'url' => $post['url_name'], 'id' => $post['id']])?>" >
                <div class="btn2-menu"><span class="under-line">Информация</span></div>
            </a>
            <a href="<?=Url::to(['post/gallery', 'name' => $post['url_name'], 'postId' => $post['id']])?>">
                <div class="btn2-menu"><span class="under-line">Фотографии <?=$photoCount?></span></div>
            </a>
            <a href="<?=Url::to(['post/reviews', 'name' => $post['url_name'], 'postId' => $post['id']])?>">
                <div class="btn2-menu active"><span class="under-line">Отзывы <?=$post['count_reviews']?></span></div>
            </a>
        </div>
    </div>
</div>
<div class="block-content">

    <div class="main_container_reviews">

        <div class="block-write-reviews main-write" data-post_id="<?=$post->id?>">
            <div class="profile-user-reviews">
                <img class="profile-icon60x60" src="<?=Yii::$app->user->getPhoto()?>">
				<?=Yii::$app->user->getName()?>
            </div>
            <div class="container-write-reviews"></div>
            <div class="large-wide-button open-container"><p>Написать новый отзыв</p></div>
        </div>
        <?php if($reviewsDataProvider->totalCount || Yii::$app->request->get('type',false)):?>
        <div class="block-flex-white inside" style="margin-top: 30px">
            <div class="block-content">
                <div class="menu-btns-card feeds-btn-bar">
                    <a href="<?=Url::to(['post/reviews','name' => $post['url_name'], 'postId' => $post['id']])?>">
                        <div class="btn2-menu <?=($type === 'all') ? 'active' : ''?>">
                            <span class="under-line">Все</span>
                        </div>
                    </a>
                    <a href="<?=Url::to(['post/reviews', 'type' => 'positive','name' => $post['url_name'], 'postId' => $post['id']])?>">
                        <div class="btn2-menu <?=($type === 'positive') ? 'active' : ''?>">
                            <span class="under-line">Положительные</span>
                        </div>
                    </a>
                    <a href="<?=Url::to(['post/reviews', 'type' => 'negative','name' => $post['url_name'], 'postId' => $post['id']])?>">
                        <div class="btn2-menu <?=($type === 'negative') ? 'active' : ''?>">
                            <span class="under-line">Отрицательные</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <?=\app\components\cardsReviewsWidget\CardsReviewsWidget::widget([
                'dataProvider' => $reviewsDataProvider,
                'settings'=>[
                    'show-more-btn' => true,
                    'replace-container-id' => 'feed-reviews',
                    'load-time' => $loadTime,
                    'without_header'=>true,
                    'btn_sort'=>true
                ]
            ]);?>
      <?php endif;?>
        <div class="margin-top60"></div>
    </div>
</div>
<div style="margin-top: 30px"></div>
<input style="display: none" class="photo-add-review" name="photo-add-review" type="file" multiple
       accept="image/*,image/jpeg,image/gif,image/png">
<script>
	$(document).ready(function() {
		menu_control.fireMethodClose();
	})
</script>
<?php
Pjax::end();
?>
