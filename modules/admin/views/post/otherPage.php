<?php

use \yii\widgets\ActiveForm;
use yii\widgets\Pjax;

$this->title = 'Создать страницу';

Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => false,
    'id' => 'pjax-container-edit-page',
    'linkSelector' => false,
    'formSelector' => '#pjax-container-edit-page form',
]);
?>

    <div class="margin-top60"></div>
    <div class="block-content">
        <h1 class="h1-c" style="margin-top: 35px">Добавить страницу</h1>
        <div class="container-add-place container-feedback" style="margin-top: 30px">
            <?php $form = ActiveForm::begin([
                'id' => 'form-edit-page',
                'enableClientScript' => false,
                'action' => '/admin/post/other-page',
                'options' => ['pjax-container-edit-page' => 'true'],
            ]) ?>
            <div class="block-field-setting">
                <label class="label-field-setting">URL страницы</label>
                <?= $form->field($editPage, 'url_name')
                    ->textInput([
                        'style' => 'margin-bottom: 15px;',
                        'class' => 'input-field-setting',
                        'placeholder' => 'Вставьте адрес страницы',
                        'value' => $editPage['url_name'],
                    ])
                    ->label(false) ?>
            </div>

            <label>
                <div class="btn-send" style="z-index: 3;position: relative">
                    <div class="large-wide-button"><p>Найти</p></div>
                </div>
                <input id="btn-form-edit-page" type="submit" style="display: none;">
            </label>
            <?php ActiveForm::end() ?>

        </div>
        <div class="margin-top60"></div>
    </div>
<?php

if (isset($toastMessage)) {
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