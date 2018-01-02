<?php
use app\models\DescriptionPage;
use app\widgets\cardsDiscounts\CardsDiscounts;
use yii\helpers\Url;
use \yii\widgets\Pjax;
use \app\components\breadCrumb\BreadCrumb;
use \app\components\Helper;

$totalCount = $dataProvider->getTotalCount();
$categoryText = $this->context->under_category?$this->context->under_category['name']:$this->context->category['name'];;
$city_name = Yii::$app->city->getSelected_city()['name'];
$cityUrl = Yii::$app->city->getSelected_city()['url_name'];
$categoryUrl = $this->context->under_category['url_name'] ?? $this->context->category['url_name'];

$h1_text = 'Скидки на ' . $categoryText.' в '.Yii::t('app/locativus',$city_name);

$descriptionText = '';
$descriptionPage = [];

if(!$this->context->under_category){
    $descriptionPage = DescriptionPage::initMetaTags(function() use ($h1_text,$totalCount){
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

    $descriptionPage = DescriptionPage::initMetaTags(function () use ($categoryText, $city_name, $totalCount, $h1_text) {
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
            <a href="/<?=$urlPost?>?sort=_rating"
               class="btn-sort"><span class="under-line">По рейтингу</span></a>
            <a href="/<?=$urlPost?>?sort=new"
               class="btn-sort"><span class="under-line">Новые</span></a>
            <?php if(Yii::$app->request->cookies->getValue('geolocation')):?>
                <a href="/<?=$urlPost?>?sort=nigh"
                   class="btn-sort"><span class="under-line">Рядом</span></a>
            <?php else:?>
                <a style="display: none" href="/<?=$urlPost?>?sort=nigh"
                   class="btn-nigh btn-sort"><span class="under-line">Рядом</span></a>
                <a class="btn-sort no-geolocation"><span class="under-line">Рядом</span></a>
            <?php endif;?>
            <a href="/<?=Yii::$app->request->getPathInfo()?>" class="btn-sort active">
                <span class="under-line">Скидки <?=$totalCount?></span>
            </a>
        </div>
    </div>
</div>
<div class="block-content">
    <?php if ($totalCount > 0):?>
        <div class="cards-block-discount" data-favorites-state-url="/discount/favorite-state">
            <?= CardsDiscounts::widget([
                'dataProvider' => $dataProvider,
                'settings' => [
                    'show-more-btn' => true,
                    'replace-container-id' => 'feed-discounts',
                    'loadTime' => $loadTime,
                    'postId' => false,
                    'show-distance' => true,
                ]
            ]); ?>
        </div>
    <?php else:?>
        <div style="margin-top: 10px; display: flex"></div>
        <div class="container-message">
            <div class="message-filter">
                <p>В Скидки пока ничего не добавили</p>
            </div>
        </div>
    <?php endif;?>

</div>