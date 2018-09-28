<?php foreach ($data as $item): ?>
    <div class="row-content">
        <span class="r1"><?= $item->discount->data ?></span>
        <span class="r2"><?= $item->promo_code ?></span>
        <span class="r3"><?= Yii::$app->formatter->asDate($item->date_buy + $timezone,
                'dd.MM.yyyy') ?></span>
        <span class="r4">до <?= Yii::$app->formatter->asDate($item->date_finish + $timezone,
                'dd.MM.yyyy') ?></span>
        <span class="r5"><?= $item->price ?></span>
        <span class="r6">
            <?php if ($item->isActive()): ?>
                <div class="block-promo-pin">
              <div class="text-pin2">Пин-код</div>
                <input type="text">
              <div class="btn-enter-pin" data-id="<?= $item->id ?>" data-type="certificate">Ввод</div>
            </div>
            <?php else: ?>
                <span class="confirm-order-btn"></span>
            <?php endif; ?>
        </span>
    </div>
<?php endforeach; ?>


