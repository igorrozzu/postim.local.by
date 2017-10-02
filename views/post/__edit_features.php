<?php foreach ($features->rubrics as $feature):?>
    <?php if($feature->type == 2):?>
        <div class="block-field-setting" id="<?=$feature->id?>">
            <label class="label-field-setting"><?= $feature->name?></label>
            <input name="features[<?=$feature->id?>]" class="input-field-setting validator" data-error-parents="block-field-setting" data-regex="^[0-9]*[.,]?[0-9]{0,}$" data-message="Некорректно введены данные" placeholder="Введите <?=$feature->name?>" value="<?=$feature->value?>">
        </div>
    <?php else:?>
        <div class="block-field-setting" id="<?=$feature->id?>">
            <label class="label-field-setting"><?=$feature->name?></label>
            <div class="selectorFields" data-is-many="true" data-id="features[<?=$feature->id?>]" data-max="1000" data-info='<?=\yii\helpers\Json::encode($feature->underFeatures)?>'>
                <div class="block-inputs">
                    <?php foreach ($feature->underFeatures as $underFeature):?>
                        <?php if($underFeature['value']!=null):?>
                            <div class="btn-selected-option">
                                <span class="option-text"><?=$underFeature['name']?></span>
                                <span class="close-selected-option"></span>
                                <input name="features[<?=$underFeature['main_features']?>][]" value="<?=$underFeature['id']?>" style="display: none">
                            </div>
                        <?php endif;?>
                    <?php endforeach;?>

                </div>
                <div class="between-selected-field btn-open-field" data-open="false">
                    <input class="search-selected-field" data-value="Выберите" value="Выберите" placeholder="Выберите" type="button">
                    <div class="open-select-field2"></div>
                </div>
                <div class="container-scroll-fields">
                    <div class="container-options"></div>
                </div>
            </div>
        </div>
    <?php endif;?>
<?php endforeach;?>
<?php if($features->additionally):?>
<div class="block-field-setting" id="additionally">
    <label class="label-field-setting">Особенности</label>
    <div class="selectorFields" data-is-many="true" data-id="features[additionally]" data-max="1000"
         data-info='<?= \yii\helpers\Json::encode($features->additionally) ?>'>
        <div class="block-inputs">
			<?php foreach ($features->additionally as $underFeature): ?>
				<?php if ($underFeature['value'] != null): ?>
                    <div class="btn-selected-option">
                        <span class="option-text"><?= $underFeature['name'] ?></span>
                        <span class="close-selected-option"></span>
                        <input name="features[additionally][]" value="<?= $underFeature['id'] ?>" style="display: none">
                    </div>
				<?php endif; ?>
			<?php endforeach; ?>

        </div>
        <div class="between-selected-field btn-open-field" data-open="false">
            <input class="search-selected-field" data-value="Выберите" value="Выберите особенности" placeholder="Выберите особенности"
                   type="button">
            <div class="open-select-field2"></div>
        </div>
        <div class="container-scroll-fields">
            <div class="container-options"></div>
        </div>
    </div>
</div>
<?php endif;?>

