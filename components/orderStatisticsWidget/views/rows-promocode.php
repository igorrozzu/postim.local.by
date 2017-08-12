<?php foreach ($data as $item):?>
    <div class="row-content">
        <span class="r1"><?=$item->discount->data?></span>
        <span class="r2"><?=$item->promo_code?></span>
        <span class="r3"><?=Yii::$app->formatter->asDate($item->date_buy + $timezone,
                'dd.MM.yyyy')?></span>
        <span class="r4">до <?=Yii::$app->formatter->asDate($item->discount->date_finish + $timezone,
                'dd.MM.yyyy')?></span>
        <span class="r5"><?=$item->discount->price?></span>
        <span class="r6">
            <?php if($item->isActive()): ?>
                <span class="close-promocode" data-id="<?=$item->id?>" data-type="promocode"></span>
            <?php else:?>
                <span class="confirm-order-btn"></span>
            <?php endif;?>
        </span>
    </div>
<?php endforeach;?>


