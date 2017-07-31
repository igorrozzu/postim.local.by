<?php
use app\components\cardsNewsWidget\CardsNewsWidget;
use \app\components\breadCrumb\BreadCrumb;

$this->title = $h1;
?>
<div class="margin-top60"></div>
<div class="block-content">
    <?= BreadCrumb::widget(['breadcrumbParams'=>$breadcrumbParams])?>
    <h1 class="h1-v"><?=$h1?></h1>
</div>
<div class="block-content">
    <div class="block-news-container">
        <div style="margin-top: 10px"></div>
        <div class="block-news">
            <?= CardsNewsWidget::widget(['dataprovider' => $dataProvider, 'settings' => ['replace-container-id' => 'feed-news','load-time'=>$loadTime]]) ?>
        </div>
    </div>
</div>
<div class="clear-fix"></div>
<div style="margin-top: 30px"></div>


