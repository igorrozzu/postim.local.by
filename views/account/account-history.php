<?php

use app\components\breadCrumb\BreadCrumb;
use app\widgets\accountStatistic\AccountStatistic;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;


$this->title = 'История вашего счета';

Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => true,
    'id' => 'account-feeds',
    'linkSelector' => '#account-feeds .menu-btns-card a',
    'formSelector' => false,
])
?>
<div class="block-content" style="margin-top: 80px;">
    <?= BreadCrumb::widget(['breadcrumbParams' => $breadcrumbParams]) ?>
    <h1 class="h1-v">История вашего счета</h1>

</div>
<div class="block-flex-white">
    <div class="block-content">
        <div class="menu-btns-card">

            <a href="<?= Url::to(['account/replenishment']) ?>">
                <div class="btn2-menu"><span class="under-line">Пополнить</span></div>
            </a>
            <a href="<?= Url::to(['account/history']) ?>">
                <div class="btn2-menu active"><span class="under-line">История</span></div>
            </a>
        </div>
    </div>
</div>

<div class="block-content">
    <div class="std-container">
        <div class="fill-account-header">
            На счету: <?= $userInfo->virtual_money ?> руб
        </div>

        <div class="statistic-content">
            <?php if ($dataProvider->getTotalCount() > 0): ?>
                <table class="statistic-table">
                    <?= AccountStatistic::widget([
                        'dataProvider' => $dataProvider,
                        'settings' => [
                            'show-more-btn' => true,
                            'replace-container-id' => 'item-table-statistic',
                            'load-time' => $loadTime,
                        ],
                    ]) ?>
                </table>
            <?php else: ?>
                <p class="card-text-notice">История счета не найдена</p>
            <?php endif; ?>
        </div>
    </div>


</div>

<script>
	$(document).ready(function () {
		main.initCustomScrollBar($('.statistic-content'), {axis: "x", scrollInertia: 50})
	});
</script>

<?php
Pjax::end();
?>
