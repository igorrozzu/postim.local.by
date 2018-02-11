<?php

use app\components\breadCrumb\BreadCrumb;
use app\components\orderStatisticsWidget\OrderStatisticsWidget;
use yii\helpers\Url;
use yii\widgets\Pjax;

$currentPage = $dataProvider->pagination->getPage();
$searchUrl = $dataProvider->pagination->createUrl($currentPage);
$dataProvider->pagination->selfParams['order_time'] = false;
$currentUrl = $dataProvider->pagination->createUrl($currentPage);
$current_month = (int)date('n');
$time_prev_month = mktime(0,0,0, $current_month !== 1 ? $current_month - 1 : 12, 1);

$this->title = 'Заказы промокодов на Postim.by';
?>
<div class="margin-top60"></div>
<div class="block-content">
    <?= BreadCrumb::widget(['breadcrumbParams'=>$breadcrumbParams])?>
    <h1 class="h1-v">Заказы промокодов</h1>
</div>
<?php
Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => false,
    'id' => 'feeds-of-user',
    'linkSelector' => '#feeds-of-user .feeds-btn-bar a',
    'formSelector' => false,
])
?>

<div class="block-content">
    <div class="container-promo-statistic">
        <div class="search-promo-statistic">
            <input class="search-by-promo-code" placeholder="Поиск по названию и промокоду" data-href="<?=$searchUrl?>">
        </div>
        <div class="horizontal-scroll12">
            <div class="block-sort-menu feeds-btn-bar">
                <a href="<?=$currentUrl?>" <?=$order_time === null ? 'class="active"' : ''?>>За все время</a>
                <a href="<?=$currentUrl?>&order_time=today" <?=$order_time === 'today' ? 'class="active"' : ''?>>
                    За сегодня</a>
                <a href="<?=$currentUrl?>&order_time=yesterday" <?=$order_time === 'yesterday' ? 'class="active"' : ''?>>
                    За вчера</a>
                <a href="<?=$currentUrl?>&order_time=current-month" <?=$order_time === 'current-month' ? 'class="active"' : ''?>>
                    За <?=Yii::$app->formatter->asDate(time(), 'LLLL')?></a>
                <a href="<?=$currentUrl?>&order_time=prev-month" <?=$order_time === 'prev-month' ? 'class="active"' : ''?>>
                    За <?=Yii::$app->formatter->asDate($time_prev_month , 'LLLL')?></a>
            </div>
            <div class="table-container">
            <?= OrderStatisticsWidget::widget([
                'dataProvider' => $dataProvider,
                'settings' => [
                    'show-more-btn' => true,
                    'replace-container-id' => 'feed-promo',
                    'load-time' => $loadTime,
                    'view-name' => 'index',
                    'column-status-view' => 'promocode',
                    'time-range' => $timeRange,
                ]
            ]);?>
            </div>
        </div>
    </div>
</div>

<?php
Pjax::end();
?>

<div style="margin-bottom:30px;"></div>
