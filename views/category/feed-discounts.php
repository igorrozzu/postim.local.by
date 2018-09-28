<?php

use app\components\MetaTagsSocialNetwork;
use app\models\DescriptionPage;
use app\widgets\cardsDiscounts\CardsDiscounts;
use \yii\widgets\Pjax;
use \app\components\breadCrumb\BreadCrumb;
use \app\components\Helper;

$totalCount = $dataProvider->totalCount;
$categoryText = $this->context->under_category ?
    $this->context->under_category['name'] : $this->context->category['name'];;
$city_name = Yii::$app->city->getSelected_city()['name'];
$defaultUrl = '/' . Yii::$app->request->getPathInfo();

$h1_text = $categoryText . ' в ' . Yii::t('app/locativus', $city_name);

$descriptionText = '';
$descriptionPage = [];

$descriptionPage = DescriptionPage::initMetaTags(function () use ($h1_text, $categoryText, $city_name) {
    $response = [
        'title' => $h1_text . ': Скидки, акции и промокоды',
        'description' => $h1_text . ': Скидки, акции, промокоды — выгодные предложения по низкой цене на Postim.by.',
        'keywords' => $categoryText . ', акции, промокоды, скидки, ' . $city_name,
        'descriptionText' => $h1_text . ': скидки, акции, промокоды — выгодные предложения по низкой цене на Postim.by.',
        'h1' => $h1_text . ': скидки',
    ];

    return $response;
});

$this->title = $descriptionPage['title'];
$this->registerMetaTag([
    'name' => 'description',
    'content' => $descriptionPage['description'],
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content' => $descriptionPage['keywords'],
]);

MetaTagsSocialNetwork::registerOgTags($this, [
    'og:title' => $descriptionPage['title'],
    'twitter:title' => $descriptionPage['title'],
    'og:description' => $descriptionPage['description'],
    'twitter:description' => $descriptionPage['description'],
]);
?>
    <div class="margin-top60"></div>
    <div class="menu-info-cards-contener">
        <div class="menu-info-cards">
            <div class="btns-filter main-pjax">
                <a href="/<?= $urlPost ?>">
                    <div class="btn-division btn-bb">
                        <span class="under-line">Места <?= $postCount ?></span>
                    </div>
                </a>
                <?php if ($totalCount > 0): ?>
                    <a href="<?= $defaultUrl ?>">
                        <div id="discount-total-count" class="btn-division btn-bb active">
                            <span class="under-line">Скидки <?= $totalCount ?></span>
                        </div>
                    </a>
                <?php endif; ?>
                <div class="btn-filter btn-bb open-now" data-name_filter="open" data-value="now">
                    <span class="under-line">Открыто сейчас</span>
                </div>
            </div>
        </div>
    </div>
    <div id="map_block" class="block-map preload-map">
        <div class="btns-map">
            <div class="action-map" title="Открыть карту"></div>
            <div class="find-me" title="Найти меня"></div>
            <div class="zoom-plus"></div>
            <div class="zoom-minus"></div>
        </div>

        <div id="map" style="display: none"></div>
    </div>
    <div class="block-content">
        <?= BreadCrumb::widget(['breadcrumbParams' => $breadcrumbParams]) ?>
        <h1 class="h1-v"><?= $descriptionPage['h1'] ?></h1>
    </div>

<?php
Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => false,
    'id' => 'feed-category',
    'linkSelector' => '#feed-category .block-sort a',
    'formSelector' => false,
])
?>
    <div class="block-flex-white">
        <div class="block-content">
            <div class="block-sort">
                <a href="<?= Helper::createUrlWithSelfParams($selfParams, ['sort' => 'new']) ?>"
                   class="btn-sort <?= $sort === 'new' ? 'active' : '' ?>">
                    <span class="under-line">Новые</span>
                </a>
                <a href="<?= Helper::createUrlWithSelfParams($selfParams, ['sort' => 'popular']) ?>"
                   class="btn-sort <?= $sort === 'popular' ? 'active' : '' ?>">
                    <span class="under-line">Популярные</span>
                </a>
                <?php if (Yii::$app->request->cookies->getValue('geolocation')): ?>
                    <a href="<?= Helper::createUrlWithSelfParams($selfParams, ['sort' => 'nigh']) ?>"
                       class="btn-sort <?= $sort === 'nigh' ? 'active' : '' ?>">
                        <span class="under-line">Рядом</span>
                    </a>
                <?php else: ?>
                    <a style="display: none"
                       href="<?= Helper::createUrlWithSelfParams($selfParams, ['sort' => 'nigh']) ?>"
                       class="btn-nigh btn-sort <?= $sort === 'nigh' ? 'active' : '' ?>">
                        <span class="under-line">Рядом</span>
                    </a>
                    <a class="btn-sort no-geolocation"><span class="under-line">Рядом</span></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="block-content">
        <?php if ($totalCount > 0): ?>
            <div class="cards-block-discount row-4 main-pjax" data-favorites-state-url="/discount/favorite-state">
                <?= CardsDiscounts::widget([
                    'dataprovider' => $dataProvider,
                    'settings' => [
                        'show-more-btn' => true,
                        'replace-container-id' => 'feed-discounts',
                        'load-time' => $loadTime,
                        'postId' => false,
                        'show-distance' => true,
                    ],
                ]); ?>
            </div>
        <?php else: ?>
            <div class="container-message">
                <div class="message-filter">
                    <p>По вашим параметрам ничего не найдено</p>
                    <span>Попробуйте сбросить несколько фильтров</span>
                </div>
            </div>
        <?php endif; ?>

        <div class="description-text">
            <?= $descriptionPage['descriptionText'] ?>
        </div>
    </div>
<?php
$js = <<<js
    $(document).ready(function() {
      category.filters.setDefaultUrl({url:'$defaultUrl'});
      category.refreshDiscountTotalCount('$totalCount');
      map.setIdPlacesOnMap("$keyForMap");
    });
js;
echo "<script>$js</script>";
Pjax::end();

$settings = [
    'select_category' => $this->context->category ?? false,
    'select_under_category' => $this->context->under_category ?? false,
];

if ($settings['select_category']) {
    $select_category = $settings["select_category"]['name'];
    $select_under_category = $settings["select_under_category"]['name'] ?? 'NuN';
    $js = <<<js
        $(document).ready(function() {
          menu.openCategoryInLeftMenu('$select_category','$select_under_category');
          category.filters.init();
        });
js;
}
echo "<script>$js</script>";
?>