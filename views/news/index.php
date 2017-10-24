<?php
use app\components\cardsNewsWidget\CardsNewsWidget;
use \app\components\breadCrumb\BreadCrumb;

$descriptionText = 'Последние новости в '.Yii::t('app/locativus',Yii::$app->city->getSelected_city()['name']).' – обзоры интересных мест с фото и видео,
     а также главные события '.Yii::t('app/parental_slope',Yii::$app->city->getSelected_city()['name']).' на Postim.by!';
$this->title = $title;
$this->registerMetaTag([
    'name' => 'description',
    'content' => $descriptionText
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content'=> 'новости '.Yii::t('app/parental_slope',Yii::$app->city->getSelected_city()['name'])
]);

?>
<div class="margin-top60"></div>
<div class="block-content">
    <?= BreadCrumb::widget(['breadcrumbParams'=>$breadcrumbParams])?>
    <h1 class="h1-v"><?=$h1?></h1>
</div>
<div class="block-content">
    <div class="block-news-container">
        <div class="block-news">
            <?= CardsNewsWidget::widget(['dataprovider' => $dataProvider, 'settings' => ['replace-container-id' => 'feed-news','load-time'=>$loadTime]]) ?>
        </div>
    </div>
</div>
<div class="clear-fix"></div>
<div style="margin-top: 30px"></div>
<div class="block-content">
    <div class="description-text">
        <?=$descriptionText?>
    </div>
</div>

<script>
    $(document).ready(function() {
        menu.openPageInLeftMenu($('#btn-menu-news'));
    });
</script>
