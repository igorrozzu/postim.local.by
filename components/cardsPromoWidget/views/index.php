<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
$data = $dataProvider->getModels();
$count = count($data);
$timezone = Yii::$app->user->getTimezoneInSeconds();
?>
<?php foreach ($data as $item):?>
    <div class="card-promo">
        <div class="card-promo-photo" style="background-image: url('<?=$item->discount->getCover()?>')"></div>
        <div class="block-promo-info">
            <h3 class="promo-info-header"><?=$item->discount->header?></h3>
            <div class="container-info-btn">
                <div class="container-promo-info">
                    <div class="promo-info">Промокод<span><?=$item->promo_code?></span>
                        <?php if(isset($item->pin_code)):?>
                        <div class="text-pin">Пин-код <?=$item->pin_code?></div>
                        <?php endif;?>
                    </div>
                    <div class="promo-info">Срок действия
                        <span>
                            <?=Yii::$app->formatter->asDate($item->date_finish + $timezone,
                                'до dd.MM.yyyy')?>
                        </span>
                    </div>
                    <div class="promo-info">Куплен
                        <span>
                            <?=Yii::$app->formatter->asDate($item->date_buy + $timezone,
                                'dd.MM.yyyy в HH:mm')?>
                        </span>
                    </div>
                </div>
                <div class="block-promo-btns">
                    <div class="promo-btn btn-print-promo"></div>
                    <div class="promo-btn btn-download-promo"></div>
                    <div class="promo-btn btn-close-promo"></div>
                </div>
            </div>
        </div>
    </div>
<?php endforeach;?>

<?php if($count > 0):?>
    <?php if($settings['show-more-btn'] &&
        ($hrefNext = $dataProvider->pagination->getLinks()['next'] ?? false)):?>
        <div class="btn-show-more" id="<?=$settings['replace-container-id']?>"
             data-selector_replace="#<?=$settings['replace-container-id']?>"
             data-href="<?=$hrefNext?>&loadTime=<?=$settings['load-time']?>">
            <?=$settings['show-more-btn-text']?>
        </div>
    <?php endif;?>
<?php else:?>
    <div class="card-promo">
        <p class="card-text-notice"> <?=$settings['not-found-text']?></p>
    </div>
<?php endif;?>

