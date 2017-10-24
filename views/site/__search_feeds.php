<?php
use \yii\widgets\Pjax;

$this->title = 'Поиск на Postim.by';
?>
<div class="margin-top60"></div>
<?php
Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => false,
    'id' => 'feed-search',
    'linkSelector' => '#feed-search .block-sort a',
    'formSelector' => false,
]);
$type = Yii::$app->request->get('type_feed','place');
?>
    <div class="block-flex-white" style="margin-top: 0px">
        <div class="block-content">
            <div class="block-sort">
                <div style="display: flex;">
                    <a href="/<?=Yii::$app->request->getPathInfo()?>" class="btn-sort <?=$type=='place'?'active':''?>"><span class="under-line">Места <?=$widget_paramsPlace['dataprovider']->totalCount?></span></a>
                    <a href="/<?=Yii::$app->request->getPathInfo()?>?type_feed=news" class="btn-sort <?=$type=='news'?'active':''?>"><span class="under-line">Новости <?=$widget_paramsNews['dataprovider']->totalCount?></span></a>
                </div>
            </div>
        </div>
    </div>
<?php if (Yii::$app->request->get('type_feed', 'place')=='place'): ?>
    <?= $this->render('search/__search_' . Yii::$app->request->get('type_feed', 'place'), ['widget' => $widgetPlace, 'widget_params' => $widget_paramsPlace]) ?>
<?php else: ?>
    <?= $this->render('search/__search_' . Yii::$app->request->get('type_feed', 'place'), ['widget' => $widgetNews, 'widget_params' => $widget_paramsNews]) ?>
<?php endif; ?>
<?php
Pjax::end();
?>