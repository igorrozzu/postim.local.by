<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>

<div class="container-popup-window form-password-recovery">
    <div class="header-popup-authentication">
        <div class="logo"></div>
        <div class="close-big-icon close-notif-message"></div>
    </div>
    <div class="body-popup-authentication">
        <div class="text-label">Для изменения адреса электронной почты будет необходимо его подтверждение</div>
        <?php $form = ActiveForm::begin([
            'id' => 'change-email-form',
            'enableClientScript' => false
        ]) ?>
            <div class="field-input">
                <?= $form->field($model, 'email')
                    ->textInput(['placeholder' => 'Электронная почта'])
                    ->label(false) ?>
            </div>

        <div class="field-btn">
            <div class="btn-red" id="change-email-btn">Подтвердить</div>
        </div>
        <?php ActiveForm::end() ?>
    </div>
</div>