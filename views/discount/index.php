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
    'linkSelector' => '.container-discount-info .container-bottom-btn a',
    'formSelector' => false,
])
?>
<div class="block-content">
    <?=BreadCrumb::widget(['breadcrumbParams'=>$breadcrumbParams])?>
    <h1 class="h1-v"><?=$discount->header?></h1>
</div>

<div class="block-content">
    <div class="container-discount">
        <div class="container-discount-photos">
            <div class="discount-photos">
                <?php foreach ($discount->gallery as $photo):?>
                    <img href="<?=$discount->getPathToPicture($photo->link)?>"
                         src="<?=$discount->getPathToPicture($photo->link)?>">
                <?php endforeach;?>
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
                    <?=$discount->count_orders?> из <?=$discount->number_purchases?>
                </span>
            </div>

            <div class="discount-info-text before-icon-purse">
                Цена промокода:
                <span class="discount-info-bold-text">бесплатно</span>
            </div>
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
    $(document).ready(function () {
        $('.discount-photos').magnificPopup({
            delegate: 'img', // child items selector, by clicking on it popup will open
            type: 'image',
            gallery: {enabled: true}
        });
    });
</script>
<?php
Pjax::end();
?>
