<?php

use \yii\widgets\ActiveForm;

?>
<div class="container-popup-window form-business-account">
    <div class="header-popup-authentication">
        <div class="logo"></div>
        <div class="close-big-icon close-business-account-btn"></div>
    </div>
    <div class="body-popup-authentication">
        <div class="visible-form-business-account">
            <?php $form = ActiveForm::begin(['id' => 'form-bs-account', 'enableClientScript' => false]) ?>
            <div class="text-label complain-text">Создание бизнес-аккаунта</div>

            <?= $form->field($businessOrder, 'user_id', ['options' => ['style' => 'display:none']])
                ->textInput([
                    'class' => 'input-field-setting',
                    'placeholder' => 'id user',
                    'value' => $businessOrder['user_id'],
                ])
                ->label(false) ?>

            <?= $form->field($businessOrder, 'post_id', ['options' => ['style' => 'display:none']])
                ->textInput([
                    'class' => 'input-field-setting',
                    'placeholder' => 'id post',
                    'value' => $businessOrder['post_id'],
                ])
                ->label(false) ?>

            <?= $form->field($businessOrder, 'full_name',
                ['options' => ['class' => 'field-input', 'style' => 'margin-top: 15px;']])
                ->textInput(['placeholder' => 'Имя и фамилия', 'value' => $businessOrder['full_name']])
                ->label(false) ?>

            <?= $form->field($businessOrder, 'position',
                ['options' => ['class' => 'field-input', 'style' => 'margin-top: 10px;']])
                ->textInput(['placeholder' => 'Должность в компании', 'value' => $businessOrder['position']])
                ->label(false) ?>

            <?= $form->field($businessOrder, 'phone',
                ['options' => ['class' => 'field-input', 'style' => 'margin-top: 10px;']])
                ->textInput(['placeholder' => 'Контактный телефон', 'value' => $businessOrder['phone']])
                ->label(false) ?>

            <div class="field-btn">
                <div class="btn-red create-business-account-btn">Создать аккаунт</div>
            </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>