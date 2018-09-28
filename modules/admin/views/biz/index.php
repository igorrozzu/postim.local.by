<?php

use \yii\widgets\ActiveForm;

$this->title = 'Бизнес-аккаунты';

use yii\widgets\Pjax;

Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => false,
    'id' => 'pjax-container-add-biz',
    'linkSelector' => '#pjax-container-add-biz a',
    'formSelector' => '#pjax-container-add-biz form',
]);
?>

<div class="margin-top60"></div>
<div class="block-content">
    <h1 class="h1-c" style="margin-top: 35px">Бизнес-аккаунты</h1>

    <?php $form = ActiveForm::begin([
        'id' => 'form-add-biz',
        'enableClientScript' => false,
        'action' => '/admin/biz/save',
        'options' => ['pjax-container-add-biz' => 'true'],
    ]) ?>

    <div class="container-add-place container-feedback" style="margin-top: 30px">

        <div class="block-field-setting">
            <label class="label-field-setting">Добавить бизнес-аккаунт</label>
            <?= $form->field($biz, 'post_id')
                ->textInput([
                    'style' => 'margin-bottom: 15px;',
                    'class' => 'input-field-setting',
                    'placeholder' => 'Введите id места',
                    'value' => $biz['post_id'],
                ])
                ->label(false) ?>
        </div>
        <div class="block-field-setting">
            <?= $form->field($biz, 'owner_id')
                ->textInput([
                    'style' => 'margin-bottom: 15px;',
                    'class' => 'input-field-setting',
                    'placeholder' => 'Введите id пользователя',
                    'value' => $biz['owner_id'],
                ])
                ->label(false) ?>
        </div>

        <div class="block-field-setting">
            <?= $form->field($biz_account, 'full_name')
                ->textInput([
                    'style' => 'margin-bottom: 15px;',
                    'class' => 'input-field-setting',
                    'placeholder' => 'Введите имя и фамилию',
                    'value' => $biz_account['full_name'],
                ])
                ->label(false) ?>
        </div>

        <div class="block-field-setting">
            <?= $form->field($biz_account, 'position')
                ->textInput([
                    'style' => 'margin-bottom: 15px;',
                    'class' => 'input-field-setting',
                    'placeholder' => 'Введите должность пользователя',
                    'value' => $biz_account['position'],
                ])
                ->label(false) ?>
        </div>

        <div class="block-field-setting">
            <?= $form->field($biz_account, 'phone')
                ->textInput([
                    'style' => 'margin-bottom: 15px;',
                    'class' => 'input-field-setting',
                    'placeholder' => 'Введите телефон пользователя',
                    'value' => $biz_account['phone'],
                ])
                ->label(false) ?>
        </div>

        <label>
            <div class="btn-send" style="z-index: 3;position: relative">
                <div class="large-wide-button"><p>Добавить</p></div>
            </div>
            <input id="btn-form-edit-page" type="submit" style="display: none;">
        </label>

    </div>
    <?php ActiveForm::end() ?>

</div>


<?php

if (Yii::$app->session->hasFlash('toastMessage')) {
    $toastMessage = Yii::$app->session->getFlash('toastMessage');
    $js = <<<JS
    $(document).ready(function () {
        $().toastmessage('showToast', {
            text     : '$toastMessage[message]',
            stayTime:         5000,
            type     : '$toastMessage[type]'
        });
    });
JS;
    echo "<script>$js</script>";


}

echo $this->render('table', [
    'dataProvider' => $dataProvider,
    'searchModel' => $searchModel,
]);

echo $this->render('order_table.php', [
    'dataProviderOrder' => $dataProviderOrder,
    'searchModel' => $searchModel,
]);

?>
<div class="block-content">
    <h1 class="h1-c" style="margin-top: 35px">Добавить премиум бизнес аккаунт</h1>

    <form id="form-add-biz"
          action="/admin/biz/add-business-account"
          method="post"
          pjax-container-add-biz="true">
        <div class="container-add-place container-feedback" style="margin-top: 30px">

            <div class="block-field-setting">
                <input type="text" class="input-field-setting" name="businessAccount[postId]"
                       style="margin-bottom: 15px;" placeholder="Id места" aria-required="true">
            </div>
            <div class="block-field-setting">
                <input type="text" class="input-field-setting" name="businessAccount[userId]"
                       style="margin-bottom: 15px;" placeholder="Id пользователя" aria-required="true">
            </div>
            <div class="block-field-setting">
                <input type="text" class="input-field-setting" name="businessAccount[dayCount]"
                       style="margin-bottom: 15px;" placeholder="Количество дней премиума" aria-required="true">
            </div>
            <label>
                <div class="btn-send" style="z-index: 3;position: relative">
                    <div class="large-wide-button"><p>Добавить</p></div>
                </div>
                <input id="btn-form-edit-page" type="submit" style="display: none;">
            </label>

        </div>
    </form>
</div>

<?php

echo $this->render('premium-accounts', [
    'dataProvider' => $dataProviderPremiumAccount,
    'searchModel' => $searchModel,
]);

Pjax::end();
?>
