<?php
use \yii\widgets\ActiveForm;
$this->title = 'Добавить особенность на Postim.by';
use yii\widgets\Pjax;


Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => false,
    'id' => 'pjax-container',
    'linkSelector' => false,
    'formSelector' => '#pjax-container form',
]);

?>

<div class="margin-top60"></div>
<div class="block-content">

    <h1 class="h1-c" style="margin-top: 35px">Добавить особенность</h1>
    <?php $form = ActiveForm::begin(['id' => 'form-add', 'enableClientScript' => false,'action'=>'/admin/features/add','options'=>['pjax-container'=>'true']]) ?>

    <div class="container-add-place" style="margin-top: 30px">
        <div class="block-field-setting">
            <label class="label-field-setting">Название особенности</label>
            <?= $form->field($model, 'name')
                ->textInput(['style' => 'margin-bottom: 15px;', 'class' => 'input-field-setting',
                    'placeholder' => 'Введите текст', 'value' => $model['name']])
                ->label(false) ?>
        </div>
        <div class="block-field-setting">
            <label class="label-field-setting">ID особенности</label>
            <?= $form->field($model, 'id')
                ->textInput(['style' => 'margin-bottom: 15px;', 'class' => 'input-field-setting',
                    'placeholder' => 'Введите название особенности на транслейте', 'value' => $model['id']])
                ->label(false) ?>
        </div>


        <div class="block-field-setting" style="border-bottom: 0px">
            <label class="label-field-setting">Тип особенности</label>
            <div class="selectorFields" data-is-many="false" data-id="Features[type]" data-max="1"
                 data-info='<?= \yii\helpers\Json::encode([['id'=>1,'name'=>'Обычный'],['id'=>2,'name'=>'Цифровой'],['id'=>3,'name'=>'Множество']]) ?>'>
                <div class="block-inputs" style="display: none">
                    <div class="btn-selected-option"><span class="option-text"><?=$model->type?></span> <span class="close-selected-option"></span> <input name="Features[type]" value="<?=$model->type?>" style="display: none"> </div>
                </div>
                <div class="between-selected-field btn-open-field" data-open=false>
                    <input class="search-selected-field" type="button" data-value="<?=$model->getLabelType()?>"
                           value="<?=$model->getLabelType()?>" placeholder="Выберите">
                    <div class="open-select-field2"></div>
                </div>
                <div class="container-scroll-fields">
                    <div class="container-options"></div>
                </div>
            </div>
        </div>


    </div>

    <div class="container-add-place" style="margin-top: 30px">
        <div class="block-field-setting" style="border-bottom: 0px">
            <label class="label-field-setting">Показывать в фильтрах?</label>
            <div class="selectorFields" data-is-many="false" data-id="Features[filter_status]" data-max="1"
                 data-info='<?= \yii\helpers\Json::encode([['id'=>0,'name'=>'Нет'],['id'=>1,'name'=>'Да']]) ?>'>
                <div class="block-inputs" style="display: none">
                    <div class="btn-selected-option"><span class="option-text"><?=$model->filter_status?></span> <span class="close-selected-option"></span> <input name="Features[filter_status]" value="<?=$model->filter_status?>" style="display: none"> </div>
                </div>
                <div class="between-selected-field btn-open-field" data-open=false>
                    <input class="search-selected-field" type="button" data-value="<?=$model->getLabelFilterStatus()?>"
                           value="<?=$model->getLabelFilterStatus()?>" placeholder="Выберите">
                    <div class="open-select-field2"></div>
                </div>
                <div class="container-scroll-fields">
                    <div class="container-options"></div>
                </div>
            </div>
        </div>
        <label>
            <div class="btn-send" style="z-index: 3;position: relative">
                <div class="large-wide-button"><p>Создать особенность</p></div>
            </div>
            <input id="btn-form-edit-page" type="submit" style="display: none;">
        </label>
    </div>

    <?php
    ActiveForm::end();
    ?>




    <h1 class="h1-c" style="margin-top: 100px">Добавить подособенность</h1>
    <?php $form = ActiveForm::begin(['id' => 'form-add2', 'enableClientScript' => false,'action'=>'/admin/features/add','options'=>['pjax-container'=>'true']]) ?>

    <div class="container-add-place" style="margin-top: 30px">
        <div class="block-field-setting">
            <label class="label-field-setting">Название</label>
            <?= $form->field($modelUnder, 'name')
                ->textInput(['style' => 'margin-bottom: 15px;', 'class' => 'input-field-setting',
                    'placeholder' => 'Введите текст', 'value' => $modelUnder['name']])
                ->label(false) ?>
        </div>
        <div class="block-field-setting">
            <label class="label-field-setting">ID</label>
            <?= $form->field($modelUnder, 'id')
                ->textInput(['style' => 'margin-bottom: 15px;', 'class' => 'input-field-setting',
                    'placeholder' => 'Введите название особенности на транслейте', 'value' => $modelUnder['id']])
                ->label(false) ?>
        </div>


        <div class="block-field-setting" style="border-bottom: 0px">
            <label class="label-field-setting">Основная особенность</label>
            <div class="selectorFields" data-is-many="false" data-id="FeaturesUnder[main_features]" data-max="1"
                 data-info='<?= \yii\helpers\Json::encode($modelUnder->getListMainFeatures()) ?>'>
                <div class="block-inputs" style="display: none">
                    <div class="btn-selected-option"><span class="option-text"><?=$modelUnder->main_features?></span> <span class="close-selected-option"></span> <input name="FeaturesUnder[main_features]" value="<?=$modelUnder->main_features?>" style="display: none"> </div>
                </div>
                <div class="between-selected-field btn-open-field" data-open=false>
                    <input class="search-selected-field" type="button" data-value="<?=$modelUnder->getNameMainFeatures()?>"
                           value="<?=$modelUnder->getNameMainFeatures()?>" placeholder="Выберите особенность">
                    <div class="open-select-field2"></div>
                </div>
                <div class="container-scroll-fields">
                    <div class="container-options"></div>
                </div>
            </div>
        </div>


    </div>

    <div class="container-add-place" style="margin-top: 30px">
        <div class="block-field-setting" style="border-bottom: 0px">
            <label class="label-field-setting">Показывать в фильтрах?</label>
            <div class="selectorFields" data-is-many="false" data-id="FeaturesUnder[filter_status]" data-max="1"
                 data-info='<?= \yii\helpers\Json::encode([['id'=>0,'name'=>'Нет'],['id'=>1,'name'=>'Да']]) ?>'>
                <div class="block-inputs" style="display: none">
                    <div class="btn-selected-option"><span class="option-text"><?=$modelUnder->filter_status?></span> <span class="close-selected-option"></span> <input name="FeaturesUnder[filter_status]" value="<?=$modelUnder->filter_status?>" style="display: none"> </div>
                </div>
                <div class="between-selected-field btn-open-field" data-open=false>
                    <input class="search-selected-field" type="button" data-value="<?=$modelUnder->getLabelFilterStatus()?>"
                           value="<?=$modelUnder->getLabelFilterStatus()?>" placeholder="Выберите">
                    <div class="open-select-field2"></div>
                </div>
                <div class="container-scroll-fields">
                    <div class="container-options"></div>
                </div>
            </div>
        </div>
        <label>
            <div class="btn-send" style="z-index: 3;position: relative">
                <div class="large-wide-button"><p>Создать подособенность</p></div>
            </div>
            <input id="btn-form-edit-page" type="submit" style="display: none;">
        </label>
    </div>

    <?php
    ActiveForm::end();
    ?>

</div>


<?php

if(isset($toastMessage)) {
    $js = <<<JS
    $(document).ready(function () {
        $().toastmessage('showToast', {
            text     : '$toastMessage[message]',
            stayTime:         5000,
            type     : '$toastMessage[type]'
        });
    });
JS;
    echo "<script>$js</script>";
}

Pjax::end();
?>
