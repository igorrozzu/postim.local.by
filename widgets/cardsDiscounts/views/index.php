<?php
use yii\helpers\Url;

$discounts = $dataProvider->getModels();
?>

<?php foreach ($discounts as $discount):
    $duration = Yii::$app->formatter->asCustomDuration($discount->date_finish - time());
?>
    <div class="card-block-discount" data-item-id="<?=$discount->id?>">
        <a href="<?=Url::to(['discount/read', 'url' => $discount->url_name,
            'discountId' => $discount->id])?>" class="discount-link">
            <div class="block-discount-photo" style="background-image: url('<?=$discount->getCover();?>')">
                <div class="block-blackout-discount">
                    <div class="discount-block">
                        <?php if (isset($discount->discount)):?>
                            <div class="discount <?=!isset($duration) ? 'inactive' : ''?>">
                                -<?=$discount->discount?>%
                            </div>
                        <?php endif;?>
                    </div>
                    <div class="go_to_view-block">
                        <div class="go_to_view">Посмотреть</div>
                    </div>
                    <div class="btn-like-block">
                        <div class="btn-like <?=isset($discount->hasLike) ? 'active' : ''?>">
                            <?=$discount->count_favorites?>
                        </div>
                    </div>
                </div>
            </div>
        </a>
        <div class="block-discount">
            <div class="block-discount-info">
                <div class="discount-description"><?=$discount->header?></div>
            </div>
            <div class="block-discount-dopinfo">
                <div class="time-left">
                    <?=$duration ? 'Осталось ' . $duration : 'Акция закончилась'?>
                </div>
                <?php if ($settings['show-distance']):?>
                    <div class="distance-to-me">
                        <?=$discount->post->distanceText?>
                    </div>
                <?php endif;?>
            </div>
            <div class="discount-footer">
                <div class="promo-code-price">Бесплатно</div>
                <div class="ava"><?=$discount->count_orders?></div>
            </div>
        </div>
    </div>

<?php endforeach;?>

<?php if($settings['show-more-btn']):?>
    <?php if ($nextLink = $dataProvider->pagination->getLinks()['next'] ?? false): ?>
        <div class="replace-block mg-btm-30" id="<?=$settings['replace-container-id']?>">
            <div class="btn-show-more" data-selector_replace="#<?=$settings['replace-container-id']?>"
                 data-href="<?=$nextLink?>&loadTime=<?=$settings['loadTime']?>&postId=<?=$settings['postId'] ?? ''?>">
                <noindex>Показать больше скидок</noindex>
            </div>
        </div>

    <?php else:?>
        <div class="replace-block mg-btm-30"></div>
    <?php endif; ?>
<?php endif;?>


