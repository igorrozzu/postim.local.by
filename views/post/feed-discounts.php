<?php
use app\components\breadCrumb\BreadCrumb;
use app\widgets\cardsDiscounts\CardsDiscounts;
use yii\helpers\Url;
use yii\widgets\Pjax;
$discountCount = $dataProvider->getTotalCount();

$categoryName = mb_strtolower(Yii::t('app/singular', $post->onlyOnceCategories[0]->name));
$city = Yii::t('app/locativus', $post->city->name);

$this->title = $post->data . ' - ' .  $categoryName . ' в ' . $city . ', ' . $post['address'] . ': cкидки';

$this->registerMetaTag([
    'name' => 'description',
    'content' => 'Скидки, '. $categoryName .
        ' ' . $post->data . ' в ' . $city.
        ', ' . $post['address'] . '. Скидки заведений на Postim.by.'
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content'=> $post->data . ' скидки'
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
    'linkSelector' => '#post-feeds .menu-btns-card a,.card-block-discount a',
    'formSelector' => false,
])
?>
<div class="block-content">
    <?=BreadCrumb::widget(['breadcrumbParams'=>$breadcrumbParams])?>
    <h1 class="h1-v"><?=$post->data.' - скидки'?></h1>
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
            <?php if ($isShowDiscounts):?>
                <a href="<?=Url::to(['post/get-discounts-by-post', 'name' => $post['url_name'], 'postId' => $post['id']])?>">
                    <div class="btn2-menu active"><span class="under-line">Скидки <?=$discountCount?></span></div>
                </a>
            <?php endif;?>
        </div>
    </div>
</div>
<div class="block-content">
    <?php if (Yii::$app->user->isModerator() || isset($post->isCurrentUserOwner)):?>
        <div class="std-container">
            <a href="<?=Url::to(['discount/add', 'postId' => $post['id']]);?>"
               class="large-wide-button non-border fx-bottom ">
                <p>Добавить скидку</p>
            </a>
        </div>
    <?php endif;?>

    <?php if ($discountCount > 0):?>
        <div class="cards-block-discount" data-favorites-state-url="/discount/favorite-state">
            <?= CardsDiscounts::widget([
                'dataProvider' => $dataProvider,
                'settings' => [
                    'show-more-btn' => true,
                    'replace-container-id' => 'feed-discounts',
                    'loadTime' => time(),
                    'postId' => $post['id'],
                    'show-distance' => false,
                ]
            ]); ?>
        </div>
    <?php else:?>
        <div style="margin-top: 10px; display: flex"></div>
        <div class="container-message">
            <div class="message-filter">
                <p>Вы пока ничего не добавили в Скидки</p>
                <span>Добавляйте свои скидки, нажав на кнопку «Добавить скидку».</span>
            </div>
        </div>
    <?php endif;?>
</div>

<script>
    $(document).ready(function() {
        <?php if(Yii::$app->session->hasFlash('success')):?>
            $().toastmessage('showToast', {
                text: '<?=Yii::$app->session->getFlash('success')?>',
                stayTime: 5000,
                type: 'success'
            });
        <?php endif;?>
    });
</script>
<?php
Pjax::end();
?>
