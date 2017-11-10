<?php
use yii\authclient\widgets\AuthChoice;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="container-popup-window form-register">
    <div class="header-popup-authentication">
        <div class="logo"></div>
        <div class="close-big-icon close-sign-up"></div>
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
                   class="social-btn-<?=$client->getName()?>"></a>
            <?php endforeach; ?>
        </div>
        <?php AuthChoice::end() ?>
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
        <div class="text-label" style="font-size: 12px;">
            Авторизуясь, вы соглашаетесь с <a href="/agreement" class="btn-link" style="font-size: 12px;">
                правилами пользования сайтом</a> и даете согласие на обработку персональных данных.
        </div>
    </div>
</div>

