<?php
use app\components\cardsNewsWidget\CardsNewsWidget;
use \app\components\breadCrumb\BreadCrumb;
use app\components\rightBlock\RightBlockWidget;
use \app\models\DescriptionPage;
use app\widgets\cardsDiscounts\CardsDiscounts;

$city = Yii::$app->city->getSelected_city();
$changedCityName = Yii::t('app/locativus',  $city['name']);
$descriptionPage = DescriptionPage::initMetaTags(function () use ($city, $changedCityName) {
    $response = [
        'title' => 'Акции и скидки в ' . $changedCityName .
            ' — бесплатные промокоды на Postim.by',
        'description' => 'Бесплатные промокоды, скидки, акции, распродажи в ' .
            $changedCityName . ' — выгодные предложения по низкой цене',
        'keywords' => 'Промокоды, скидки, акции, распродажи, ' . $city['name'],
        'h1' => 'Cкидки ' . Yii::t('app/parental_slope', $city['name']),
    ];

    return $response;
});

$this->title = $descriptionPage['title'];
$this->registerMetaTag([
    'name' => 'description',
    'content' => $descriptionPage['description']
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content' => $descriptionPage['keywords']
]);

?>
<div class="margin-top60"></div>
<div class="block-content">
    <?= BreadCrumb::widget(['breadcrumbParams' => $breadcrumbParams])?>
    <h1 class="h1-v"><?=$descriptionPage['h1']?></h1>
</div>
<div class="block-content">
    <div class="container-columns">
        <div class="__first-column">
        <?php if ($dataProvider->getTotalCount() > 0): ?>
            <div class="cards-block-discount row-3 main-pjax" data-favorites-state-url="/discount/favorite-state">

                <?= CardsDiscounts::widget([
                    'dataprovider' => $dataProvider,
                    'settings' => [
                        'show-more-btn' => true,
                        'replace-container-id' => 'feed-discounts',
                        'load-time' => $loadTime,
                        'show-distance' => false,
                    ]
                ]); ?>
            </div>
        <?php else: ?>
            <div style="margin-top: 10px; display: flex"></div>
            <div class="card-promo">
                <p class="card-text-notice">Скидок не найдено</p>
            </div>
        <?php endif; ?>
        </div>
        <div class="__second-column">
            <div class="--top-30px"></div>
            <?= RightBlockWidget::widget()?>
        </div>
    </div>
</div>
<div class="clear-fix"></div>
<div style="margin-top: 30px"></div>
<div class="block-content">
    <div class="description-text --description-width">
        <?=$descriptionPage['description']?>
    </div>
</div>

<script>
    $(document).ready(function() {
        menu.openPageInLeftMenu($('#btn-all-discounts'));
    });
</script>
