<?php
use app\components\breadCrumb\BreadCrumb;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\Pjax;

$categoryName = mb_strtolower(Yii::t('app/singular', $post->onlyOnceCategories[0]->name));
$city = Yii::t('app/locativus', $post->city->name);

$this->title = $post->data . ' - ' .  $categoryName . ' в ' . $city . ', ' . $post['address'] . ': cкидки';

$this->registerMetaTag([
    'name' => 'description',
    'content' => 'Скидки, '. $categoryName .
        ' ' . $post->data . ' в ' . $city.
        ', ' . $post['address'] . '. Скидки заведений на Postim.by.'
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content'=> $post->data . ' скидки'
]);
?>

<?php
Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => true,
    'id' => 'post-feeds',
    'linkSelector' => '.container-discount-info .container-bottom-btn a',
    'formSelector' => false,
])
?>

<div class="margin-top60"></div>
<div class="block-content">
    <?=BreadCrumb::widget(['breadcrumbParams'=>$breadcrumbParams])?>
    <h1 class="h1-v"><?=$discount->header?></h1>
</div>

<div class="block-content">

    <?php if(Yii::$app->user->isModerator()):?>
        <div class="block-content-between" style="margin-bottom: -10px">
            <p class="text p-text">
                Нашли неточность или ошибку,&nbsp;
                <a class="href-edit" href="<?=Url::to(['/admin/discount/edit',
                    'id' => $discount->id, 'redirect_back' => 'true']);?>">
                    исправьте&nbsp;или&nbsp;дополните&nbsp;информацию
                </a>
            </p>
        </div>
    <?php endif;?>

    <div class="container-discount">
        <div class="container-discount-photos">
            <div class="discount-photos">
                <?php foreach ($discount->gallery as $photo):?>
                    <img href="<?=$discount->getPathToPicture($photo->link)?>"
                         src="<?=$discount->getPathToPicture($photo->link)?>">
                <?php endforeach;?>
            </div>
        </div>
        <div class="container-discount-info">
            <div class="discount-info-time-left">
                <?php
                $dateFinish = Yii::$app->formatter->asDate(
                    $discount->date_finish + Yii::$app->user->identity->getTimezoneInSeconds(),
                    'dd.MM.yyyy');
                echo $duration ? 'Акция действует до ' . $dateFinish :
                    'Акция закончилась ' . $dateFinish;
                ?>
            </div>

            <?php if (isset($discount->price)):?>
                <div class="discount-info-text">
                    Стоимость
                    <span class="through">
                        <?=$discount->price?>
                    </span>
                    <span class="discount-info-bold-text">
                        <?=$discount->price - $economy?> руб
                    </span>
                </div>
            <?php endif;?>

            <div class="discount-info-text">
                Скидка
                <span class="discount-info-bold-text">
                    <?=$discount->discount?>%
                </span>
            </div>

            <div class="discount-info-text">
                Экономия
                <span class="discount-info-bold-text">
                    <?=isset($economy) ? $economy . ' руб' : 'Не ограничена'?>
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
            <div class="container-bottom-btn">
                <div class="blue-btn-40 order-discount <?=$duration ? 'active' : 'inactive' ?>"
                     data-href="<?=Url::to(['/discount/order', 'discountId' => $discount->id])?>">
                    <p>Получить скидку <?=$discount->discount?>%</p>
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
        </ul>
    </div>

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
    });
</script>
<?php
Pjax::end();
?>
