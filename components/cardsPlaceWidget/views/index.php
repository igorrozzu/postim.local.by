<?php
    use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
    use app\components\cardsPlaceWidget\CardsPlaceWidget;
    $data = $dataprovider->getModels();
?>
<?php foreach ($data as $item):?>

    <div class="card-block" data-item-id="<?=$item['id']?>" data-type="post">
        <div class="main-pjax">
            <a href="/<?=$item['url_name']?>-p<?=$item['id']?>">
                <div class="card-photo" style="background-image: url('<?=$item["cover"]?>')">
                    <div class="glass">
                        <div class="reviews-btn-icn">
                            <div class="rating bg-r<?=$item["rating"]?>"><?=$item["rating"]?></div>
                            <div class="total-reviews"><?=$item["count_reviews"]?> отзывов</div>
                        </div>
                        <div class="bookmarks-btn<?=$item->is_like?' active':''?>">
                            <?=$item["count_favorites"]?>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="card-block-info">
            <p class="info-head"><?=CardsPlaceWidget::renderCategories($item->categories,$item->city)?></p>
            <p class="card-info"><?=Html::encode($item['data'])?></p>
        </div>
        <div class="time-work">
            <?=$item->is_open?'<p class="open">Открыто</p>':'<p class="close">Закрыто</p>'?>

        </div>
        <hr class="hr-c">
        <div class="info-address">
            <div class="address-icon"></div>
            <div class="address-text"><?=$item["address"]?></div>
        </div>
    </div>
<?php endforeach;?>

<?php if($this->context->settings['show-more-btn']):?>
    <?php if ($hrefNext = $dataprovider->pagination->getLinks()['next'] ?? false): ?>
        <div class="replace-block mg-btm-30" id="<?=$settings['replace-container-id']?>">
            <div class="btn-show-more" data-selector_replace="#<?=$settings['replace-container-id']?>"
                 data-href="<?=$hrefNext?>&loadTime=<?=$settings['load-time'] ?? ''?>">
                Показать больше мест</div>
        </div>

    <?php else:?>
        <div class="replace-block mg-btm-30">

        </div>
    <?php endif; ?>
<?php endif;?>


