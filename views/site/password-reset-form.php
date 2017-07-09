<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="container-blackout-popup-window">
    <div class="container-popup-window form-password-recovery">
        <div class="header-popup-authentication">
            <div class="logo"></div>
        </div>
        <div class="body-popup-authentication">
            <div class="text-label">Укажите новый пароль</div>
            <?php $form = ActiveForm::begin(['id' => 'password-reset-form', 'enableClientScript' => false]) ?>
                <div class="field-input">
                    <?= $form->field($model, 'password')
                        ->passwordInput(['placeholder' => 'Пароль'])
                        ->label(false) ?>
                </div>
                <div class="field-input">
                    <?= $form->field($model, 'password_repeat')
                        ->passwordInput(['placeholder' => 'Пароль еще раз'])
                        ->label(false) ?>
                </div>
                <div class="field-btn">
                    <input class="btn-red" type="submit" value="Назначить пароль" style="border: none;">
                </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>