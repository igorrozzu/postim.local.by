<?php
use \app\components\Helper;
?>
<div class="menu-content">
<div class="container-filters">
    <?php foreach ($features->rubrics as $feature):?>
        <?php if($feature->type==3):?>
            <?php
            $underFeatures = [];
            foreach ($feature->underFeatures as $underFeature){
                $underFeatures[]=['id_filter'=>$underFeature->id,'name_filter'=>Helper::mb_ucasefirst($underFeature->name),'value'=>'true'];
            }
            ?>
            <div class="btn-container-filters icon-plus" data-under_features='<?=\yii\helpers\Json::encode($underFeatures)?>' data-name_filter="<?=$feature['id']?>" data-value=true>
                <span class="name_filters"><?=Helper::mb_ucasefirst($feature['name'])?> </span>
                <span class="count"></span>
                <span class="icon-add-filters"></span>
            </div>
        <?php elseif($feature->type==2):?>

            <div class="average-item" id="<?=$feature->id?>" data-max="<?=$feature->max?>" data-min="<?=$feature->min?>">
                <div class="average-item-label">
                    <p><?=Helper::mb_ucasefirst($feature->name)?></p>
                    <p class="average-item-text">
                        <span class="min-border"></span> - <span class="max-border"></span> <span class="check-cost"><?=Helper::getCostFeature($feature)?></span>
                    </p>
                </div>
                <div  data-name_filter="<?=$feature->id?>" class="average-item-range"></div>
            </div>

        <?php endif;?>
    <?php endforeach;?>
    <?php
    $under_additionally=[];
    foreach ($features->additionally as $item){
        $under_additionally[]=['id_filter'=>$item->id,'name_filter'=>Helper::mb_ucasefirst($item->name),'value'=>'true'];
    }
    ?>
    <?php if($under_additionally):?>
        <div class="btn-container-filters " data-under_features='<?=\yii\helpers\Json::encode($under_additionally)?>' data-name_filter="#additionally" data-value=true>
            <span class="name_filters">Особенности</span>
            <span class="count"></span>
            <span class="icon-add-filters"></span>
        </div>
    <?php endif;?>
    <div class="btn-filter" data-name_filter="open" data-value="now"><span class="name_filter">Открыто сейчас</span></div>
</div>
</div>

