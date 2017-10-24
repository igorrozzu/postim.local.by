<?php
use \yii\widgets\Pjax;
use \yii\widgets\ActiveForm;

$this->title = 'Обратная связь на Postim.by';

Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => false,
    'id' => 'pjax-container-feedback',
    'linkSelector' => false,
    'formSelector' => '#pjax-container-feedback form',
]);
?>

<div class="margin-top60"></div>
<div class="block-content">
    <h1 class="h1-c" style="margin-top: 35px">Обратная связь</h1>

    <div class="container-add-place container-feedback" style="margin-top: 30px">
        <?php $form = ActiveForm::begin(['id' => 'form-feedback', 'enableClientScript' => false,'action'=>'/feedback','options'=>['pjax-container-feedback'=>'true']]) ?>
            <div class="block-field-setting">
                <label class="label-field-setting">Тема обращения</label>
                <?= $form->field($feedBack, 'subject')
                    ->textInput(['style' => 'margin-bottom: 15px;', 'class' => 'input-field-setting',
                        'placeholder' => 'Введите тему', 'value' => $feedBack['subject']])
                    ->label(false) ?>
            </div>
            <div class="block-field-setting">
                <label class="label-field-setting">Электронная почта</label>
                <?= $form->field($feedBack, 'email')
                    ->textInput(['style' => 'margin-bottom: 15px;', 'class' => 'input-field-setting',
                        'placeholder' => 'Введите email', 'value' => $feedBack['email']])
                    ->label(false) ?>
            </div>
            <div class="block-field-setting">
                <label class="label-field-setting">Текст вашего сообщения</label>
                <?= $form->field($feedBack, 'message')
                    ->textarea(['style' => 'margin-bottom: -10px;height: 44px;resize: none;', 'class' => 'input-field-setting',
                        'placeholder' => 'Введите текст', 'value' => $feedBack['message']])
                    ->label(false) ?>
            </div>
            <label>
                <div class="btn-send" style="z-index: 3;position: relative">
                    <div class="large-wide-button"><p>Отправить</p></div>
                </div>
                <input id="btn-form-feedback" type="submit" style="display: none;">
            </label>


       <?php ActiveForm::end()?>
    </div>
    <div class="margin-top60"></div>
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

$js2 = <<<js2
    $(document).ready(function() {
      $('.container-feedback textarea').autosize();
      menu_control.fireMethodClose();
    });
js2;
echo "<script>$js2</script>";
Pjax::end();
?>