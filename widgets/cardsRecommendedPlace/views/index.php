<?php

use app\widgets\cardsRecommendedPlace\CardsRecommendedPlace;
use yii\helpers\Html;

$data = $dataprovider->getModels();
?>
<?php foreach ($data as $item):?>

    <div class="card-block" data-item-id="<?=$item['id']?>" data-type="post">
        <div class="main-pjax">
                <a href="/<?=$item['url_name']?>-p<?=$item['id']?>"
                    <?= isset($settings['links-no-follow']) ? 'rel="nofollow"' : ''?>>

                <div class="card-photo lazy" data-src="<?=$item["cover"]?>" style="background-image: url('/post-img/default.png')">
                    <div class="glass">
                        <div class="sign-recommended-post">Рекомендуем</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="js-href-post">
            <div class="card-block-info">
                <p class="info-head">
                    <?=CardsRecommendedPlace::renderTextCategories($item->categories)?>
                </p>
                <p class="card-info">
                    <?=Html::encode(Html::decode($item['data']))?>
                </p>
            </div>

        </div>
    </div>
<?php endforeach;?>


