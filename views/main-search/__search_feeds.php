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

?>

<div class="block-flex-white" style="margin-top: 0px">
    <div class="block-content">
        <div class="block-sort">
            <div style="display: flex;">
                <a href="/<?= $url?>" class="btn-sort
                <?= $type === 'post' ? 'active' : ''?>">
                    <span class="under-line">
                        Места <?=$widgets['post']['params']['dataprovider']->totalCount?>
                    </span>
                </a>
                <a href="/<?= $url?>?type_feed=news" class="btn-sort
                <?= $type ==='news' ? 'active' : '' ?>">
                    <span class="under-line">
                        Новости <?=$widgets['news']['params']['dataprovider']->totalCount?>
                    </span>
                </a>
                <a href="/<?= $url?>?type_feed=discount" class="btn-sort
                <?= $type ==='discount' ? 'active' : '' ?>">
                    <span class="under-line">
                        Скидки <?=$widgets['discount']['params']['dataprovider']->totalCount?>
                    </span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php
 echo $this->render('search/__search_' . $type, [
     'widget' => $widgets[$type],
 ]);
Pjax::end();
?>