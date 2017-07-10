<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="container-popup-window form-password-recovery">
    <div class="header-popup-authentication">
        <div class="logo"></div>
        <div class="close-big-icon close-recovery-btn"></div>
    </div>
    <div class="body-popup-authentication">
        <div class="text-label">Укажите электронную почту</div>
        <?php $form = ActiveForm::begin(['id' => 'password-recovery-form', 'enableClientScript' => false]) ?>
        <div class="field-input">
            <?= $form->field($model, 'email')
                ->textInput(['placeholder' => 'Электронная почта'])
                ->label(false) ?>
        </div>
        <div class="field-btn">
            <div class="btn-red" id="btn-password-recovery">Сбросить пароль</div>
        </div>
        <?php ActiveForm::end() ?>
        <div class="field-line-btns">
            <a class="btn-link sign-in-btn">Авторизация</a>
            <a class="btn-link sign-up-btn">Регистрация</a>
        </div>
    </div>
</div>