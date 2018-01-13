<?php
$totalCount = $dataProvider->getTotalCount();
$totalPrice = $totalCount > 0 ? $dataProvider->query->sum('price'): null;
?>

<div class="table-promo">
    <div class="row-header">
        <span class="r1">Название</span>
        <span class="r2">Промокод</span>
        <span class="r3">Куплен</span>
        <span class="r4">Срок действия</span>
        <span class="r5">Сумма, руб</span>
        <span class="r6">Клиент</span>
    </div>
    <div class="row-content">
        <span class="r1">
            <?php if($settings['column-status-view'] === 'certificate'):?>
                Сертификатов
            <?php else:?>
                Промокодов
            <?php endif;?>
            (<?=$totalCount?>), статистика за <?=$settings['time-range'] ? '(' . $settings['time-range'] . ')' : 'все время'?>
        </span>
        <span class="r2">-</span>
        <span class="r3">-</span>
        <span class="r4">-</span>
        <span class="r5"><?=$totalPrice ?? '-';?></span>
        <span class="r6">-</span>
    </div>
<?php if($totalCount > 0) {
    echo $this->render('rows', [
        'dataProvider'=> $dataProvider,
        'settings' => $settings
    ]);
};
?>
</div>
<script>
    $(document).ready(function () {
        businessAccount.statisticTableScrollInit();
    });
</script>

