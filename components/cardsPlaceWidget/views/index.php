<?php
    use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
?>
<?php foreach ($dataprovider as $item):?>
    <div class="card-block ">
        <a href="/<?=$item['url_name']?>">
            <div class="card-photo" style="background-image: url('<?=$item["cover"]?>')">
                <div class="glass">
                    <div class="reviews-btn-icn">
                        <div class="rating bg-r<?=$item["rating"]?>"><?=$item["rating"]?></div>
                        <div class="total-reviews"><?=$item["count_reviews"]?> отзывов</div>
                    </div>
                    <div class="bookmarks-btn<?=$item->is_like?'-active':''?>"><?=$item["count_favorites"]?></div>
                </div>
            </div>
        </a>


        <div class="card-block-info">
            <p class="info-head"><?=$item['categories']['name']?></p>
            <p class="card-info"><?=Html::encode($item['data'])?></p>
        </div>
        <div class="time-work">
            <?=$item->is_open?'<p class="open">Открыто</p>':'<p class="open">Закрыто</p>'?>

        </div>
        <hr class="hr-c">
        <div class="info-address">
            <div class="address-icon"></div>
            <div class="address-text"><?=$item["address"]?></div>
        </div>
    </div>
<?php endforeach;?>

<?php if($this->context->settings['show-more-btn']):?>
    <div class="replace-block mg-btm-30">
        <div class="btn-show-more">Показать больше мест</div>
    </div>
<?php endif;?>


