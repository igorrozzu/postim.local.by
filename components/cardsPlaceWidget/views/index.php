<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\components\cardsPlaceWidget\CardsPlaceWidget;
use yii\helpers\Json;
use \app\widgets\LinkPager\LinkPager;

$data = $dataprovider->getModels();
$CurrentMePosition = Yii::$app->request->cookies->getValue('geolocation') ?
    Json::decode(Yii::$app->request->cookies->getValue('geolocation')) : false;
$oldFormatter = new \app\components\OldFormatter();

if (isset($settings['is-it-sphinx-model'])) {
    $data = ArrayHelper::getColumn($data, 'post');
}
?>
<?php foreach ($data as $item):?>

    <div class="card-block" data-item-id="<?=$item['id']?>" data-type="post">
        <div class="main-pjax">
            <?php if($settings['moderation'] ?? false): ?>
                <a href="/<?=$item['url_name']?><?=$item['main_id']!=null?'-p'.$item['main_id']:''?>-v<?=$item['id']?>">
            <?php else:?>
                <a href="/<?=$item['url_name']?>-p<?=$item['id']?>">
            <?php endif;?>

                <div class="card-photo lazy" data-src="<?=$item["cover"]?>" style="background-image: url('/post-img/default.png')">
                    <div class="glass">
                        <div class="reviews-btn-icn">
                            <div class="rating bg-r<?=$item["rating"]?>"><?=$item["rating"]?></div>
                            <div class="total-reviews">
                                <?=$item["count_reviews"]?>
                                <?=Yii::$app->formatter->getNumEnding($item["count_reviews"], [
                                        'отзыв', 'отзыва', 'отзывов'
                                ])?>
                            </div>
                        </div>
                        <div class="bookmarks-btn<?=$item->is_like?' active':''?>">
                            <?=$item["count_favorites"]?>
                        </div>
                        <?php if($item->hasActualDiscounts):?>
                            <div class="has-discounts">Здесь акция!</div>
                        <?php endif;?>
                    </div>
                </div>
            </a>
        </div>
        <div class="js-href-post">
            <div class="card-block-info">
                <p class="info-head"><?=CardsPlaceWidget::renderCategories($item->categories,$item->city)?></p>
                <p class="card-info"><?=Html::encode(Html::decode($item['data']))?></p>
            </div>
            <div class="time-work">
                <?=$item->is_open?'<p class="open">Открыто '.$item->timeOpenOrClosed.'</p>':
                    '<p class="close">Закрыто '.$item->timeOpenOrClosed.'</p>'?>
                <?php if($item->distanceText):?>
                    <div class="distance-to-me">
                        <?=$item->distanceText?>
                    </div>
                <?php endif;?>

            </div>
            <hr class="hr-c">
            <div class="info-address">
                <div class="address-icon"></div>
                <div class="address-block">
                    <div class="address-text"><?=$item->city['name'].', '.$item["address"]?></div>
                </div>
            </div>
        </div>

    </div>
<?php endforeach;?>

<?php if($this->context->settings['show-more-btn']):?>
    <?php if ($hrefNext = $dataprovider->pagination->getLinks()['next'] ?? false): ?>
        <div class="replace-block mg-btm-30" id="<?=$settings['replace-container-id']?>">
            <div class="btn-show-more" data-selector_replace="#<?=$settings['replace-container-id']?>"
                 data-href="<?=$hrefNext?>&loadTime=<?=$settings['load-time'] ?? ''?><?= isset($settings['load-geolocation'])&&is_array($settings['load-geolocation'])?'&'.http_build_query(array('load-geolocation'=>$settings['load-geolocation'])):''?>">
                <noindex>Показать больше мест</noindex></div>
        </div>

    <?php else:?>
        <div class="replace-block mg-btm-30">

        </div>
    <?php endif; ?>

<?php elseif($this->context->settings['show-pagination'] ?? false):?>
    <div class="replace-block main-pjax">
        <?php
        echo LinkPager::widget([
            'pagination' => $dataprovider->pagination,
            'extraQuery' => ('&loadTime=' . $settings['load-time'] ?? '') . (isset($settings['load-geolocation'])&&is_array($settings['load-geolocation'])?'&'.http_build_query(array('load-geolocation'=>$settings['load-geolocation'])):'')
        ]);
        ?>
    </div>
<?php endif;?>


