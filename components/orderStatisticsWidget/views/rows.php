<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
$data = $dataProvider->getModels();
$timezone = Yii::$app->user->identity->getTimezoneInSeconds();
?>
<?=$this->render('rows-' . $settings['column-status-view'], [
    'data'=> $data,
    'timezone' => $timezone
])?>

<?php if($settings['show-more-btn'] &&
    ($hrefNext = $dataProvider->pagination->getLinks()['next'] ?? false)):?>
    <div class="large-wide-button non-border btn-load-more" id="<?=$settings['replace-container-id']?>"
         data-selector_replace="#<?=$settings['replace-container-id']?>"
         data-href="<?=$hrefNext?>&loadTime=<?=$settings['load-time']?>&only_rows=true">
        <p>Показать больше заказов</p>
    </div>
<?php else:?>
    <div class="row-content-footer">
        <span class="r1">-</span>
        <span class="r2">-</span>
        <span class="r3">-</span>
        <span class="r4">-</span>
        <span class="r5">-</span>
        <span class="r6">-</span>
    </div>
<?php endif;?>


