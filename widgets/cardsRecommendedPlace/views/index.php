<?php
use yii\helpers\Html;
use app\components\cardsPlaceWidget\CardsPlaceWidget;

$data = $dataprovider->getModels();
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
                        <div class="sign-recommended-post">Рекомендуем</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="js-href-post">
            <div class="card-block-info">
                <p class="info-head"><?=CardsPlaceWidget::renderCategories($item->categories, $item->city)?></p>
                <p class="card-info">
                    <?=Html::encode(Html::decode($item['data']))?>
                </p>
            </div>

        </div>
    </div>
<?php endforeach;?>


