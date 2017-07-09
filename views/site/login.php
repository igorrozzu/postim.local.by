<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="container-blackout-popup-window" >
    <div class="container-popup-window form-login">
        <div class="header-popup-authentication">
            <div class="logo"></div>
            <div class="close-big-icon close-sign-in"></div>
        </div>
        <div class="body-popup-authentication">
            <div class="text-label">Используйте социальные сети для входа:</div>
            <div class="block-social-authentication">
                <a class="social-btn-vk"></a>
                <a class="social-btn-fb"></a>
                <a class="social-btn-tw"></a>
                <a class="social-btn-ok"></a>
                <a class="social-btn-google"></a>
            </div>
            <div class="text-label">Или войдите как пользователь сайта:</div>
            <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientScript' => false]) ?>
                <div class="field-input">
                    <?= $form->field($model, 'email')
                        ->textInput(['placeholder' => 'Электронная почта'])
                        ->label(false) ?>
                </div>
                <div class="field-input">
                    <?= $form->field($model, 'password')
                        ->passwordInput(['placeholder' => 'Пароль'])
                        ->label(false) ?>
                </div>
                <div class="field-btn">
                    <div class="btn-red" id="btn-login">Войти</div>
                </div>
            <?php ActiveForm::end() ?>
            <div class="field-line-btns">
                <a class="btn-link sign-up-btn">Регистрация</a>
                <a class="btn-link recovery-btn">Забыли пароль?</a>
            </div>
        </div>
    </div>

</div>