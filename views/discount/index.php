<?php
use app\components\breadCrumb\BreadCrumb;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\Pjax;

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
    'linkSelector' => '#post-feeds .menu-btns-card a,.container-discount-info .container-bottom-btn a',
    'formSelector' => false,
])
?>
<div class="block-content">
    <?=BreadCrumb::widget(['breadcrumbParams'=>$breadcrumbParams])?>
    <h1 class="h1-v"><?=$discount->header?></h1>
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
                <div class="btn2-menu"><span class="under-line">Отзывы <?=$post['count_reviews']?></span></div>
            </a>
            <a href="<?=Url::to(['post/get-discounts-by-post', 'name' => $post['url_name'], 'postId' => $post['id']])?>">
                <div class="btn2-menu active"><span class="under-line">Скидки <?=$discountCount?></span></div>
            </a>
        </div>
    </div>
</div>
<div class="block-content">
    <div class="container-discount">
        <div class="container-discount-photos">
            <div class="discount-photos" style="background-image: url('testP.png')">
                <div class="container-photos-inside">
                    <div class="pre-photo"></div>
                    <div class="next-photo"></div>
                </div>
            </div>
        </div>
        <div class="container-discount-info">
            <div class="discount-info-time-left">
                Акция действует до
                <?=Yii::$app->formatter->asDate(
                        $discount->date_finish + Yii::$app->user->identity->getTimezoneInSeconds(),
                    'dd.MM.yyyy');?>
            </div>
            <?php if (isset($discount->price)):?>
                <div class="discount-info-text">
                    Стоимость
                    <span class="through">
                        <?=$discount->price?>
                    </span>
                    <span class="discount-info-bold-text">
                        <?=$discount->price - $economy?> руб
                    </span>
                </div>
            <?php endif;?>
            <div class="discount-info-text">
                Скидка
                <span class="discount-info-bold-text">
                    <?=$discount->discount?>%
                </span>
            </div>
            <?php if (isset($economy)):?>
                <div class="discount-info-text">
                    Экономия
                    <span class="discount-info-bold-text">
                        <?=$economy?> руб
                    </span>
                </div>
            <?php endif;?>
            <div class="discount-info-text before-icon-user">
                Купили
                <span class="discount-info-bold-text">
                    <?=$orderCount?> из <?=$discount->number_purchases?>
                </span>
            </div>
            <?php if (isset($discount->price_promo)):?>
                <div class="discount-info-text before-icon-purse">
                    Цена промокода
                    <span class="discount-info-bold-text">
                    <?=round($discount->price_promo, 2)?> руб
                </span>
                </div>
            <?php endif;?>
            <div class="container-bottom-btn">
                <a href="<?=Url::to(['discount/order', 'discountId' => $discount->id])?>" class="order-discount">
                    <div class="blue-btn-40">
                        <p>Получить скидку <?=$discount->discount?>%</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <h2 class="h2-c">Описание акции</h2>
    <div class="block-description-card">
        <?=$discount->data?>
    </div>
    <h2 class="h2-c">Условия</h2>
    <div class="block-description-card">
        <ul>
            <?php foreach (Json::decode($discount->conditions) as $condition): ?>
                <li><span><?=$condition?></span></li>
            <?php endforeach;?>
        </ul>
    </div>

    <div class="block-content-between">
        <noindex>
            <div class="block-social-share">
                <div class="social-btn-share goodshare" data-type="vk"><p>Поделиться</p> <span data-counter="vk">0</span></div>
                <div class="social-btn-share goodshare" data-type="fb"><p>Share</p><span data-counter="fb">0</span></div>
                <div class="social-btn-share goodshare" data-type="tw"><p>Твитнуть</p></div>
                <div class="social-btn-share goodshare" data-type="ok"><span data-counter="ok">0</span></div>
            </div>
        </noindex>
        <div class="block-count-views">
            <div class="elem-count-views"><?=$discount->totalView->count?></div>
        </div>
    </div>

</div>
<script>
</script>
<?php
Pjax::end();
?>
