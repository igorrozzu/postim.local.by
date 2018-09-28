<?php

use \yii\widgets\ActiveForm;

?>
<div class="container-popup-window form-business-account --300h">
    <div class="header-popup-authentication">
        <div class="logo"></div>
        <div class="close-big-icon close-business-account-btn"></div>
    </div>
    <div class="body-popup-authentication">
        <div class="visible-form-business-account">
            <?php $form = ActiveForm::begin(['id' => 'form-bs-account', 'enableClientScript' => false]) ?>
            <div class="text-label complain-text">Продвижение бизнеса</div>

            <?= $form->field($businessOrder, 'full_name',
                ['options' => ['class' => 'field-input', 'style' => 'margin-top: 15px;']])
                ->textInput(['placeholder' => 'Имя и фамилия', 'value' => $businessOrder['full_name']])
                ->label(false) ?>

            <?= $form->field($businessOrder, 'company_name',
                ['options' => ['class' => 'field-input', 'style' => 'margin-top: 10px;']])
                ->textInput(['placeholder' => 'Название компании', 'value' => $businessOrder['company_name']])
                ->label(false) ?>

            <?= $form->field($businessOrder, 'position',
                ['options' => ['class' => 'field-input', 'style' => 'margin-top: 10px;']])
                ->textInput(['placeholder' => 'Должность в компании', 'value' => $businessOrder['position']])
                ->label(false) ?>

            <?= $form->field($businessOrder, 'phone',
                ['options' => ['class' => 'field-input', 'style' => 'margin-top: 10px;']])
                ->textInput(['placeholder' => 'Контактный телефон', 'value' => $businessOrder['phone']])
                ->label(false) ?>

            <?= $form->field($businessOrder, 'email',
                ['options' => ['class' => 'field-input', 'style' => 'margin-top: 10px;']])
                ->textInput(['placeholder' => 'Адрес электронной почты', 'value' => $businessOrder['email']])
                ->label(false) ?>


            <?= $form->field($businessOrder, 'type',
                ['options' => ['class' => 'field-input', 'style' => 'margin-top: 10px;']])
                ->hiddenInput(['value' => $businessOrder['type']])
                ->label(false) ?>

            <div class="field-btn">
                <div class="btn-red js-create-bsa">Отправить заявку</div>
            </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>