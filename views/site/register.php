<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="container-blackout-popup-window" >
    <div class="container-popup-window form-register">
        <div class="header-popup-authentication">
            <div class="logo"></div>
            <div class="close-big-icon close-sign-up"></div>
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
            <div class="text-label">Или зарегистрируйте акаунт на сайте:</div>
            <?php $form = ActiveForm::begin(['id' => 'register-form', 'enableClientScript' => false]) ?>
                <div class="field-input">
                    <?= $form->field($model, 'name')
                        ->textInput(['placeholder' => 'Имя'])
                        ->label(false) ?>
                </div>
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
                <div class="field-input">
                    <?= $form->field($model, 'password_repeat')
                        ->passwordInput(['placeholder' => 'Пароль еще раз'])
                        ->label(false) ?>
                </div>
                <div class="field-btn">
                    <div class="btn-red" id="btn-register">Регистрация</div>
                </div>
            <?php ActiveForm::end() ?>

            <div class="field-line-btns">
                <a class="btn-link sign-in-btn">Авторизация</a>
                <a class="btn-link recovery-btn">Забыли пароль?</a>
            </div>
        </div>
    </div>

</div>

