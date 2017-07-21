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
        <div class="text-label">Подтвердите ваши данные (имя, email)</div>
        <?php $form = ActiveForm::begin([
            'id' => 'required-fields-form',
            'enableClientScript' => false
        ]) ?>
            <div class="field-input">
                <?= $form->field($model, 'name')
                    ->textInput(['placeholder' => 'Имя', 'value' => $name ?? ''])
                    ->label(false) ?>
            </div>
            <div class="field-input">
                <?= $form->field($model, 'email')
                    ->textInput(['placeholder' => 'Электронная почта'])
                    ->label(false) ?>
            </div>

            <div class="field-btn">
                <input class="btn-red" type="submit" value="Подтвердить" style="border: none;">
            </div>
        <?php ActiveForm::end() ?>
    </div>
</div>
