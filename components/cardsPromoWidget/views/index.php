<?php

use app\models\entities\DiscountOrder;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$data = $dataProvider->getModels();
$count = count($data);
$timezone = Yii::$app->user->getTimezoneInSeconds();
?>
<?php foreach ($data as $item):
    $discountUrl = Url::to(['discount/read', 'url' => $item->discount->url_name,
        'discountId' => $item->discount->id]);
?>
    <div class="card-promo">
        <a href="<?=$discountUrl?>">
            <div class="card-promo-photo" style="background-image: url('<?=$item->discount->getCover()?>')"></div>
        </a>
        <div class="block-promo-info">
            <a href="<?=$discountUrl?>">
                <h3 class="promo-info-header"><?=$item->discount->header?></h3>
            </a>
            <div class="container-info-btn">
                <div class="container-promo-info">
                    <div class="promo-info">Промокод<span><?=$item->promo_code?></span>
                        <?php if(isset($item->pin_code)):?>
                        <div class="text-pin">Пин-код <?=$item->pin_code?></div>
                        <?php endif;?>
                    </div>
                    <div class="promo-info">Срок действия
                        <span>
                            <?=Yii::$app->formatter->asDate($item->discount->date_finish + $timezone,
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
                    <?php if($item->status_promo === DiscountOrder::STATUS['active']):?>
                        <div class="promo-btn btn-close-promo"
                             data-href="<?=Url::to(['user/confirm-used-order', 'id' => $item->id])?>"></div>
                    <?php endif;?>
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

