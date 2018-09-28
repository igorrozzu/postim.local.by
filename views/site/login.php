<?php

use yii\authclient\widgets\AuthChoice;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-popup-window form-login">
    <div class="header-popup-authentication">
        <div class="logo"></div>
        <div class="close-big-icon close-sign-in"></div>
    </div>
    <div class="body-popup-authentication">
        <div class="text-label">Используйте социальные сети для входа:</div>
        <?php $authAuthChoice = AuthChoice::begin([
            'baseAuthUrl' => ['social-auth/auth'],
            'autoRender' => false,
        ]); ?>
        <div class="block-social-authentication">
            <?php foreach ($authAuthChoice->getClients() as $client): ?>
                <a href="<?= $authAuthChoice->createClientUrl($client) ?>"
                   class="social-btn-<?= $client->getName() ?>"></a>
            <?php endforeach; ?>
        </div>
        <?php AuthChoice::end() ?>
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
        <div class="text-label" style="font-size: 12px;">
            Авторизуясь, вы соглашаетесь с <a href="/agreement" class="btn-link" style="font-size: 12px;">
                правилами пользования сайтом</a> и даете согласие на обработку персональных данных.
        </div>
    </div>
</div>