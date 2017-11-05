<?php
use \yii\widgets\ActiveForm;
$this->title = 'Добавить категорию на Postim.by';
use yii\widgets\Pjax;


Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => false,
    'id' => 'pjax-container-categories',
    'linkSelector' => false,
    'formSelector' => '#pjax-container-categories form',
]);

?>

<div class="margin-top60"></div>
<div class="block-content">

    <h1 class="h1-c" style="margin-top: 35px">Добавить категорию</h1>
    <?php $form = ActiveForm::begin(['id' => 'form-add-category2', 'enableClientScript' => false,'action'=>'/admin/post/category-save','options'=>['pjax-container-categories'=>'true']]) ?>

    <div class="container-add-place" style="margin-top: 30px">
        <div class="block-field-setting">
            <label class="label-field-setting">Название категории</label>
            <?= $form->field($modelCategory, 'name')
                ->textInput(['style' => 'margin-bottom: 15px;', 'class' => 'input-field-setting',
                    'placeholder' => 'Введите текст', 'value' => $modelCategory['name']])
                ->label(false) ?>
        </div>
        <div class="block-field-setting">
            <label class="label-field-setting">Url под категории</label>
            <?= $form->field($modelCategory, 'url_name')
                ->textInput(['style' => 'margin-bottom: 15px;', 'class' => 'input-field-setting',
                    'placeholder' => 'Введите текст', 'value' => $modelCategory['url_name']])
                ->label(false) ?>
        </div>

        <label>
            <div class="btn-send" style="z-index: 3;position: relative">
                <div class="large-wide-button"><p>Создать категорию</p></div>
            </div>
            <input id="btn-form-edit-page" type="submit" style="display: none;">
        </label>
    </div>

    <?php
    ActiveForm::end();
    ?>


    <h1 class="h1-c" style="margin-top: 35px">Добавить подкатегорию</h1>
    <?php $form = ActiveForm::begin(['id' => 'form-add-category', 'enableClientScript' => false,'action'=>'/admin/post/under-category-save','options'=>['pjax-container-categories'=>'true']]) ?>

    <div class="container-add-place" style="margin-top: 30px">
        <div class="block-field-setting">
            <label class="label-field-setting">Название подкатегории</label>
            <?= $form->field($model, 'name')
                ->textInput(['style' => 'margin-bottom: 15px;', 'class' => 'input-field-setting',
                    'placeholder' => 'Введите текст', 'value' => $model['name']])
                ->label(false) ?>
        </div>
        <div class="block-field-setting">
            <label class="label-field-setting">Url под категории</label>
            <?= $form->field($model, 'url_name')
                ->textInput(['style' => 'margin-bottom: 15px;', 'class' => 'input-field-setting',
                    'placeholder' => 'Введите текст', 'value' => $model['url_name']])
                ->label(false) ?>
        </div>

        <div class="block-field-setting" style="border-bottom: 0px">
            <label class="label-field-setting">Категория</label>
            <div class="selectorFields" data-is-many="false" data-id="UnderCategory[category_id]" data-max="1"
                 data-info='<?= \yii\helpers\Json::encode($categories) ?>'>
                <div class="block-inputs" style="display: none">
                    <?php if($model->category_id):?>
                        <div class="btn-selected-option"><span class="option-text"><?=$model->category->name?></span> <span class="close-selected-option"></span> <input name="UnderCategory[category_id]" value="<?=$model->category->id?>" style="display: none"> </div>
                    <?php endif;?>
                </div>
                <div class="between-selected-field btn-open-field" data-open=false>
                    <input class="search-selected-field" type="button" data-value="Выберите категорию"
                           value="<?=$model->category_id?$model->category->name:'Выберите категорию'?>" placeholder="Выберите категорию">
                    <div class="open-select-field2"></div>
                </div>
                <div class="container-scroll-fields">
                    <div class="container-options"></div>
                </div>
            </div>
        </div>
        <label>
            <div class="btn-send" style="z-index: 3;position: relative">
                <div class="large-wide-button"><p>Создать подкатегорию</p></div>
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
