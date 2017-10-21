<?php
use app\components\cardsPlaceWidget\CardsPlaceWidget;
use app\components\cardsReviewsWidget\CardsReviewsWidget;
use yii\helpers\Url;
use yii\widgets\Pjax;
use \app\components\breadCrumb\BreadCrumb;

$this->title = $h1;
?>
<div class="margin-top60"></div>
<div class="block-content">
	<?= BreadCrumb::widget(['breadcrumbParams'=>$breadcrumbParams])?>
    <h1 class="h1-v"><?=$h1?></h1>
</div>
<?php
Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => false,
    'id' => 'feeds-of-user',
    'linkSelector' => '.feeds-btn-bar a',
    'formSelector' => false,
])
?>
<div class="block-flex-white">
    <div class="block-content">
        <div class="menu-btns-card feeds-btn-bar">
            <a href="/<?=Yii::$app->request->getPathInfo()?>">
                <div class="btn2-menu <?=($type === 'all') ? 'active' : ''?>">
                    <span class="under-line">Все</span>
                </div>
            </a>
            <a href="/<?=Yii::$app->request->getPathInfo().'?type=positive'?>">
                <div class="btn2-menu <?=($type === 'positive') ? 'active' : ''?>">
                    <span class="under-line">Положительные</span>
                </div>
            </a>
            <a href="/<?=Yii::$app->request->getPathInfo().'?type=negative'?>">
                <div class="btn2-menu <?=($type === 'negative') ? 'active' : ''?>">
                    <span class="under-line">Отрицательные</span>
                </div>
            </a>
        </div>
    </div>
</div>

<div class="block-content">
    <?php if($dataProvider->getTotalCount()):?>
        <div class="">
            <?= CardsReviewsWidget::widget([
                'dataProvider' => $dataProvider,
                'settings' => [
                    'show-more-btn' => true,
                    'replace-container-id' => 'feed-all-reviews',
                    'load-time' => $loadTime,
                ]
            ]); ?>
    <?php else:?>
        <div class="card-promo">
            <p class="card-text-notice">Отзывов не найдено</p>
        </div>
    <?php endif;?>

    </div>
</div>
<div class="clear-fix"></div>
<div class="mg-btm-30"></div>
<?php
	$js = <<<js
        $(document).ready(function() {
          menu.openPageInLeftMenu($('#btn-all-reviews'));
        });
js;
	echo "<script>$js</script>";

Pjax::end();
?>
