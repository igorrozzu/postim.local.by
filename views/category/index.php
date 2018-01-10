<?php
use \app\components\mainMenu\MainMenuWidget;
use \app\components\cardsPlaceWidget\CardsPlaceWidget;
use \app\components\cardsNewsWidget\CardsNewsWidget;
use \app\components\ListCityWidget\ListCityWidget;
use yii\helpers\Url;
use \yii\widgets\Pjax;
use \app\components\breadCrumb\BreadCrumb;
use \app\components\Helper;

$totalCount = $dataProvider->totalCount;
$categoryText = $this->context->under_category?$this->context->under_category['name']:$this->context->category['name'];
$city_name = Yii::$app->city->getSelected_city()['name'];
$h1_text=$categoryText.' в '.Yii::t('app/locativus',$city_name);
$descriptionText = '';

$descriptionPage = [];

if(!$this->context->under_category){
    $descriptionPage = \app\models\DescriptionPage::initMetaTags(function() use ($h1_text,$totalCount){
        $response = [
            'title' => $h1_text.': адреса, фото, отзывы — лучшие места',
            'description' => $h1_text.' по отзывам посетителей. Места с адресами, фото и телефонами, найдено '.$totalCount.' — удобный поиск на карте Postim.by!',
            'keywords' => $h1_text,
            'descriptionText' => $h1_text.' по отзывам посетителей. Места с адресами, фото и телефонами, найдено '.
                $totalCount.' — удобный поиск на карте Postim.by!</br> 
        Время работы и прочую информацию смотрите у нас на сайте.',
            'h1' => $h1_text,
        ];

        return $response;
    });

}else{

    $descriptionPage = \app\models\DescriptionPage::initMetaTags(function () use ($categoryText, $city_name, $totalCount, $h1_text) {
        $response = [
            'title' => $categoryText . ' ' . Yii::t('app/parental_slope', $city_name) .
                ': адреса, фото, отзывы — лучшие ' . mb_strtolower($categoryText),

            'description' => $description = 'Лучшие ' . mb_strtolower($categoryText) .
                ' ' . Yii::t('app/parental_slope', $city_name) .
                ' по отзывам посетителей. ' . $categoryText .
                ' с адресами, фото и телефонами, найдено ' .
                $totalCount . ' — удобный поиск на карте Postim.by!',

            'keywords' => mb_strtolower($categoryText) . ' ' . Yii::t('app/parental_slope', $city_name),

            'descriptionText' => 'Лучшие ' . mb_strtolower($categoryText) .
                ' ' . Yii::t('app/parental_slope', $city_name) . ' по отзывам посетителей. ' .
                $categoryText
                . ' с адресами, фото и телефонами, найдено ' .
                $totalCount . ' — удобный поиск на карте Postim.by!</br> Время работы 
        и прочую информацию смотрите у нас на сайте.',

            'h1' => $h1_text,
        ];

        return $response;
    });

}

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
<div class="menu-info-cards-contener">
    <div class="menu-info-cards">
        <div class="btns-filter main-pjax">
            <div class="btn-division btn-bb active total-count">
                <span class="under-line">Места <?=$totalCount?></span>
            </div>
            <?php if($discountCount > 0):?>
                <a href="/<?= Yii::$app->request->getPathInfo()?>/skidki">
                    <div id="discount-total-count" class="btn-division btn-bb">
                        <span class="under-line">Скидки <?=$discountCount?></span>
                    </div>
                </a>
            <?php endif;?>
            <div class="btn-filter btn-bb open-now" data-name_filter="open" data-value="now">
                <span class="under-line">Открыто сейчас</span>
            </div>
        </div>
        <div class="btn-filter icon-filter <?=!$issetFilters?'hide-dp':''?>">
            <span>Все фильтры</span>
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
    <?= BreadCrumb::widget(['breadcrumbParams'=>$breadcrumbParams])?>
    <h1 class="h1-v"><?=$descriptionPage['h1']?></h1>
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
            <a href="<?=Helper::createUrlWithSelfParams($selfParams,['sort'=>'_rating'])?>" class="btn-sort <?=$sort=='_rating'?'active':''?>"><span class="under-line">По рейтингу</span></a>
            <a href="<?=Helper::createUrlWithSelfParams($selfParams,['sort'=>'new'])?>" class="btn-sort <?=$sort=='new'?'active':''?>"><span class="under-line">Новые</span></a>
            <?php if(Yii::$app->request->cookies->getValue('geolocation')):?>
                <a href="<?=Helper::createUrlWithSelfParams($selfParams,['sort'=>'nigh'])?>" class="btn-sort <?=$sort=='nigh'?'active':''?>"><span class="under-line">Рядом</span></a>
            <?php else:?>
                <a style="display: none" href="<?=Helper::createUrlWithSelfParams($selfParams,['sort'=>'nigh'])?>" class="btn-nigh btn-sort <?=$sort=='nigh'?'active':''?>"><span class="under-line">Рядом</span></a>
                <a class="btn-sort no-geolocation"><span class="under-line">Рядом</span></a>
            <?php endif;?>
        </div>
    </div>
</div>
<div class="block-content">
    <?php if($dataProvider->totalCount):?>
        <div class="cards-block">
            <?= CardsPlaceWidget::widget([
                'dataprovider' => $dataProvider,
                'settings'=>[
                    'show-more-btn'=>true,
                    'replace-container-id' => 'feed-posts',
                    'load-time' => $loadTime,
                    'load-geolocation'=>$loadGeolocation
                ]
            ]); ?>
        </div>
    <?php elseif(Yii::$app->request->get('filters',false)):?>
        <div class="container-message">
            <div class="message-filter">
                <p>По вашим параметрам ничего не найдено</p>
                <span>Попробуйте сбросить несколько фильтров</span>
            </div>
        </div>
    <?php else:?>
    <div class="container-message">
        <p>К сожалению, в <?=Yii::t('app/locativus',Yii::$app->city->getSelected_city()['name'])?> на сайте <?=ucfirst(Yii::$app->getRequest()->serverName)?> в категории <?=$categoryText?> пока нет ничего. Если вы знаете о подходящем месте, добавьте его через кнопку <a class="add-place-href">"Добавить место"</a>. За размещение новых мест на сайте мы начисляем бонусы пользователям.</p>
    </div>
    <?php endif;?>

    <div class="description-text">
        <?=$descriptionPage['descriptionText']?>
    </div>

</div>
<?php
$defaultUrl='/'.Yii::$app->request->getPathInfo();
$js = <<<js
    $(document).ready(function() {
      category.filters.setDefaultUrl({url:'$defaultUrl'});
      category.filters.addParamOther('sort','$sort')
      category.refreshTotalCount('$totalCount');
      map.setIdPlacesOnMap("$keyForMap");
    });
js;
echo "<script>$js</script>";
Pjax::end();

$settings= [
    'select_category'=>$this->context->category ?? false,
    'select_under_category'=>$this->context->under_category ?? false
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
