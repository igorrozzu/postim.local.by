<?php
use app\components\orderStatisticsWidget\OrderStatisticsWidget;
use yii\helpers\Url;
use yii\widgets\Pjax;

$currentPage = $dataProvider->pagination->getPage();
$searchUrl = $dataProvider->pagination->createUrl($currentPage);
$dataProvider->pagination->selfParams['order_time'] = false;
$currentUrl = $dataProvider->pagination->createUrl($currentPage);
$current_month = (int)date('n');
$time_prev_month = mktime(0,0,0, $current_month !== 1 ? $current_month - 1 : 12, 1);
?>
<div class="margin-top60"></div>
<div class="block-content">
    <div class="bread-crumb">
        <a class="pre" href="#">Главная</a>
        <span class="separator"></span>
        <p>Заказы сертификатов</p>
    </div>
    <h1 class="h1-v">Заказы сертификатов</h1>
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
<div class="block-flex-white">
    <div class="block-content">
        <div class="menu-btns-card feeds-btn-bar">
            <a href="<?=Url::to(['user/zakazy-sertifikatov'])?>" >
                <div class="btn2-menu <?=($status === 'all') ? 'active' : ''?>">
                    Все <?=$countItems['all']?>
                </div>
            </a>
            <a href="<?=Url::to(['user/zakazy-sertifikatov', 'status' => 'unactive', 'type' => 'certificate'])?>">
                <div class="btn2-menu <?=($status === 'unactive') ? 'active' : ''?>">
                    Использованные <?=$countItems['inactive']?>
                </div>
            </a>
            <a href="<?=Url::to(['user/zakazy-sertifikatov', 'status' => 'active', 'type' => 'certificate'])?>">
                <div class="btn2-menu <?=($status === 'active') ? 'active' : ''?>">
                    Действующие <?=$countItems['active']?>
                </div>
            </a>
        </div>
    </div>
</div>

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
                        'column-status-view' => 'certificate',
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
