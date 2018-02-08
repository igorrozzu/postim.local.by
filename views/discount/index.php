<?php
use app\components\breadCrumb\BreadCrumb;
use app\components\cardsPlaceWidget\CardsPlaceWidget;
use app\components\MetaTagsSocialNetwork;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\Pjax;

$this->title = !empty($discount->title) ? $discount->title : $discount->header . ' на Postim.by';
$description = !empty($discount->description) ? $discount->description :
    'Промокод на скидку от ' . $post->data . '. ' . $discount->header . ' на Postim.by.';

$this->registerMetaTag([
    'name' => 'description',
    'content' => $description
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content'=> !empty($discount->key_word) ? $discount->key_word : 'скидка, промокод, акция, ' . $post->data
]);

$discountCover = $discount->getCover();
$defaultImgUrl = Yii::$app->request->getHostInfo() . $discountCover;

MetaTagsSocialNetwork::registerOgTags($this, [
    'og:title' => $this->title,
    'twitter:title' => $this->title,
    'og:description' => $description,
    'twitter:description' => $description,
    'og:type' => 'article',
    'og:image' => $defaultImgUrl,
    'twitter:image:src' => $defaultImgUrl,
]);
?>

<?php
Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => true,
    'id' => 'discount-index',
    'linkSelector' => '.container-discount-info .container-bottom-btn a',
    'formSelector' => false,
])
?>

<div class="margin-top60"></div>
<div class="block-content">
    <?=BreadCrumb::widget(['breadcrumbParams'=>$breadcrumbParams])?>
    <h1 class="h1-v"><?=$discount->header?></h1>
</div>

<div class="block-content main-pjax">

    <?php if(Yii::$app->user->isModerator() || $isCurrentUserOwner):?>
        <div class="block-content-between" style="margin-bottom: -10px">
            <p class="text p-text">
                Нашли неточность или ошибку,&nbsp;
                <a class="href-edit" href="<?=Url::to(['/discount/edit', 'id' => $discount->id])?>">
                    исправьте&nbsp;или&nbsp;дополните&nbsp;информацию
                </a>
            </p>
        </div>
    <?php endif;?>

    <div class="container-discount">
        <div class="container-discount-photos">
            <div class="discount-photos">
                <img href="<?=$discountCover?>" src="<?=$discountCover?>">
                <?php foreach ($discount->gallery as $photo):
                    $pathToPicture = $discount->getPathToPicture($photo->link); ?>

                    <?php if($pathToPicture !== $discountCover):?>
                    <img href="<?=$pathToPicture?>" src="<?=$pathToPicture?>">
                <?php endif;?>

                <?php endforeach;?>
            </div>
        </div>
        <div class="container-discount-info">
            <div class="discount-info-time-left">
                <?php
                $dateFinish = Yii::$app->formatter->asDate(
                    $discount->date_finish + Yii::$app->user->getTimezoneInSeconds(),
                    'dd.MM.yyyy');
                echo $duration ? 'Акция действует до ' . $dateFinish :
                    'Акция закончилась ' . $dateFinish;
                ?>
            </div>

            <?php

            if (isset($discount->price) || isset($discount->price_with_discount)):?>
                <div class="discount-info-text">
                    Стоимость
                    <?php if (isset($discount->price)):?>
                        <span class="<?= isset($discount->price_with_discount) ? 'through': 'discount-info-bold-text'?>">
                            <?= number_format($discount->price, 2)?>
                        </span>
                    <?php endif;?>

                    <?php if (isset($discount->price_with_discount)):?>
                        <span class="discount-info-bold-text">
                            <?= number_format($discount->price_with_discount, 2)?> руб
                        </span>
                    <?php endif;?>

                </div>
            <?php endif;?>

            <?php if (isset($discount->discount)):?>
                <div class="discount-info-text">
                    Скидка
                    <span class="discount-info-bold-text">
                    -<?=$discount->discount?>%
                    </span>
                </div>
            <?php endif;?>

            <div class="discount-info-text">
                Экономия
                <span class="discount-info-bold-text">
                    <?=isset($economy) ? number_format($economy, 2) . ' руб' : 'Не ограничена'?>
                </span>
            </div>

            <div class="discount-info-text before-icon-user">
                Купили
                <span class="discount-info-bold-text">
                    <?=$discount->count_orders?> из <?=$discount->number_purchases?>
                </span>
            </div>

            <div class="discount-info-text before-icon-purse">
                Цена промокода
                <span class="discount-info-bold-text">Бесплатно</span>
            </div>
            <div class="discount-btns-container">
                <div class="add-favorite btn-like <?= $discount->isLike ? 'active' : ''?>"
                     data-favorites-state-url="/discount/favorite-state" data-item-id="<?=$discount->id?>">
                    Добавить в избранное
                </div>
            </div>

            <div class="container-bottom-btn">
                <div class="blue-btn-40 order-discount <?=$duration ? 'active' : 'inactive' ?>"
                     data-href="<?=Url::to(['/discount/order', 'discountId' => $discount->id])?>">
                    <p>Получить скидку <?= isset($discount->discount) ?
                            '-' . $discount->discount . '%' : ''?></p>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($discount->data)):?>
        <h2 class="h2-c">Описание акции</h2>
        <div class="block-description-card">
            <?=$discount->data?>
        </div>
    <?php endif;?>

    <h2 class="h2-c">Условия</h2>
    <div class="block-description-card">
        <ul>
            <?php foreach (Json::decode($discount->conditions) as $condition): ?>
                <li><span><?=$condition?></span></li>
            <?php endforeach;?>
            <li>
                <span>Услуги (товары) предоставляются <?= $post->requisites?>.</span>
            </li>
            <li>
                <span>Поставщик несет полную ответственность перед
                    потребителем за достоверность информации.</span>
            </li>
        </ul>
    </div>

    <h2 class="h2-c">Где?</h2>
    <a href="<?= Url::to(['post/index', 'url' => $post->url_name, 'id' => $post->id])?>">
        <div class="discount-post-block">

            <div class="cover" style="background-image: url('<?= $post->cover?>')"></div>

            <div class="content">
                <div class="header-block">
                    <p class="header-text"><?=$post->data?></p>
                </div>
                <div class="address">
                    <span><?=$post->city->name . ', ' . $post->address?></span>
                </div>
            </div>
        </div>
    </a>


    <div class="block-content-between">
        <noindex>
            <div class="block-social-share">
                <div class="social-btn-share goodshare" data-type="vk"><p>Поделиться</p> <span data-counter="vk">0</span></div>
                <div class="social-btn-share goodshare" data-type="fb"><p>Share</p><span data-counter="fb">0</span></div>
                <div class="social-btn-share goodshare" data-type="tw"><p>Твитнуть</p></div>
                <div class="social-btn-share goodshare" data-type="ok"><span data-counter="ok">0</span></div>
            </div>
        </noindex>
        <div class="block-count-views">
            <div class="elem-count-views"><?=$discount->totalView->count?></div>
        </div>
    </div>

    <div style="margin-top: 30px" class="comments_entity_container" data-entity_type="4" data-entity_id="<?=$discount['id']?>">
        <?=$this->render('/comments/discount_comments',['dataProviderComments'=>$dataProviderComments,'totalComments'=>$discount->totalComments])?>
    </div>

</div>
<script>
    $(document).ready(function () {
        <?php if(Yii::$app->session->hasFlash('success')):?>
        $().toastmessage('showToast', {
            text: '<?=Yii::$app->session->getFlash('success')?>',
            stayTime: 5000,
            type: 'success'
        });
        <?php endif;?>

        $('.discount-photos').magnificPopup({
            delegate: 'img', // child items selector, by clicking on it popup will open
            type: 'image',
            gallery: {enabled: true}
        });
        $('html').scrollTop(0);

        $(window).resize(function () {
            var startWidth=900,
                startHgt=440,
                proportion=startWidth/startHgt;

            var container = $('.container-discount-photos');
            var width=$(container).width();
            $('.container-discount-photos').css({height:width/proportion+'px'});
        });
        $(window).resize();

        comments.init(4);
        comments.setAutoResize('.textarea-main-comment');

    });
</script>
<?php
Pjax::end();
?>