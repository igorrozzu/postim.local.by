<?php
use \yii\widgets\ActiveForm;
$this->title = 'Добавить категорию на Postim.by';
use yii\widgets\Pjax;


Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => false,
    'id' => 'pjax-container-features',
    'linkSelector' => false,
    'formSelector' => '#pjax-container-features form',
]);

?>

<div class="margin-top60"></div>
<div class="block-content">

    <h1 class="h1-c" style="margin-top: 35px">Добавить особенность</h1>
    <?php $form = ActiveForm::begin(['id' => 'form-add-features', 'enableClientScript' => false,'action'=>'/admin/features/features-save','options'=>['pjax-container-features'=>'true']]) ?>

    <div class="container-add-place" style="margin-top: 30px">
        <div class="block-field-setting">
            <label class="label-field-setting">Название особенности</label>
            <?= $form->field($model, 'name')
                ->textInput(['style' => 'margin-bottom: 15px;', 'class' => 'input-field-setting',
                    'placeholder' => 'Введите текст', 'value' => $model['name']])
                ->label(false) ?>
        </div>
        <div class="block-field-setting">
            <label class="label-field-setting">Тип особенности</label>
            <?= $form->field($model, 'type')
                ->textInput(['style' => 'margin-bottom: 15px;', 'class' => 'input-field-setting',
                    'placeholder' => 'Введите текст', 'value' => $model['type']])
                ->label(false) ?>
        </div>

        <div class="block-field-setting">
            <label class="label-field-setting">Тип особенности</label>
            <?= $form->field($model, 'filter_status')
                ->textInput(['style' => 'margin-bottom: 15px;', 'class' => 'input-field-setting',
                    'placeholder' => 'Введите текст', 'value' => $model['filter_status']])
                ->label(false) ?>
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
