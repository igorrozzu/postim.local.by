<?php use yii\helpers\Url;

foreach ($data as $item):?>
    <div class="row-content main-pjax">
        <span class="r1">
            <a href="<?=Url::to(['discount/read', 'url' => $item->discount->url_name,
                'discountId' => $item->discount->id])?>">
                <?=$item->discount->header?>
            </a>
        </span>
        <span class="r2"><?=$item->promo_code?></span>
        <span class="r3"><?=Yii::$app->formatter->asDate($item->date_buy + $timezone,
                'dd.MM.yyyy')?></span>
        <span class="r4">до <?=Yii::$app->formatter->asDate($item->discount->date_finish + $timezone,
                'dd.MM.yyyy')?></span>
        <span class="r5"><?=$item->discount->price?></span>
        <span class="r6">
            <a href="<?=Url::to(['user/index', 'id' => $item->user->id])?>">
                <?=$item->user->getFullName()?>
            </a>
        </span>
    </div>
<?php endforeach;?>


