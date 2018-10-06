<?php
use app\components\cardsNewsWidget\CardsNewsWidget;
use \app\components\breadCrumb\BreadCrumb;
use app\components\MetaTagsSocialNetwork;
use app\components\rightBlock\RightBlockWidget;
use \app\models\DescriptionPage;
use app\widgets\cardsDiscounts\CardsDiscounts;
use yii\widgets\Pjax;

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
$defaultUrl = '/' . Yii::$app->request->getPathInfo();

$this->title = $descriptionPage['title'];
$this->registerMetaTag([
    'name' => 'description',
    'content' => $descriptionPage['description']
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content' => $descriptionPage['keywords']
]);

MetaTagsSocialNetwork::registerOgTags($this, [
    'og:title' => $descriptionPage['title'],
    'twitter:title' => $descriptionPage['title'],
    'og:description' => $descriptionPage['description'],
    'twitter:description' => $descriptionPage['description'],
]);
?>
<div class="margin-top60"></div>
<div class="block-content">
    <?= BreadCrumb::widget(['breadcrumbParams' => $breadcrumbParams])?>
    <h1 class="h1-v"><?=$descriptionPage['h1']?></h1>
</div>

<?php
Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => false,
    'id' => 'feed-discounts-by-city',
    'linkSelector' => '#feed-discounts-by-city .block-sort a',
    'formSelector' => false,
])
?>

<div class="block-flex-white">
    <div class="block-content">
        <div class="block-sort">
            <a href="<?=$defaultUrl?>"
               class="btn-sort <?=$sort === 'new' ? 'active': ''?>">
                <span class="under-line">Новые</span>
            </a>
            <a href="<?= $defaultUrl?>?sort=popular"
               class="btn-sort <?=$sort === 'popular' ? 'active': ''?>">
                <span class="under-line">Популярные</span>
            </a>
            <?php if(Yii::$app->request->cookies->getValue('geolocation')):?>
                <a href="<?= $defaultUrl?>?sort=nigh"
                   class="btn-sort <?=$sort === 'nigh' ? 'active' : ''?>">
                    <span class="under-line">Рядом</span>
                </a>
            <?php else:?>
                <a style="display: none" href="<?=$defaultUrl?>?sort=nigh"
                   class="btn-nigh btn-sort <?=$sort === 'nigh' ? 'active': ''?>">
                    <span class="under-line">Рядом</span>
                </a>
                <a class="btn-sort no-geolocation"><span class="under-line">Рядом</span></a>
            <?php endif;?>
        </div>
    </div>
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
                        'show-distance' => true,
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
        <?=$descriptionPage['descriptionText']?>
    </div>
</div>

<script>
    $(document).ready(function() {
        menu.openPageInLeftMenu($('#btn-all-discounts'));
    });
</script>

<?php
Pjax::end();
?>
