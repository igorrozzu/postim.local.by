<?php
use \yii\widgets\ActiveForm;
$this->title = 'Добавить банер на Postim.by';
use yii\widgets\Pjax;

Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => false,
    'id' => 'pjax-container-moderation',
    'linkSelector' => '#pjax-container-moderation a',
    'formSelector' => '#pjax-container-moderation form',
]);


?>

<div class="margin-top60"></div>
<div class="block-content">
    <h1 class="h1-c" style="margin-top: 35px">Заменить банер</h1>

    <?php $form = ActiveForm::begin(['id' => 'form-add-baner', 'enableClientScript' => false,'action'=>'/admin/post/add-baner','options'=>['pjax-container-moderation'=>'true']]) ?>

    <div class="container-add-place container-feedback" style="margin-top: 30px">

        <div class="block-field-setting">
            <label class="label-field-setting">Заголовок страницы</label>
            <?= $form->field($baner, 'href')
                ->textInput(['style' => 'margin-bottom: 15px;', 'class' => 'input-field-setting',
                    'placeholder' => 'Введите текст', 'value' => $baner['href']])
                ->label(false) ?>
        </div>

    </div>

    <div class="container-add-place">
        <div class="container-gallery">
            <div class="gallery-header">Фото банера</div>
            <div class="block-gallery">
                <?php if($baner->src):?>
                    <div id="-1771374919" class="item-photo-from-gallery" style="background-image: url('<?=$baner->getPatchCover()?>');"> <div class="container-blackout"> <div class="header-btns">    <span class="btn-item-photo btn-close-photo-preview"></span> </div>  </div> </div>
                <?php endif;?>
            </div>
            <div class="btn-add-photo-preview">Добавить фотографии</div>
            <div class="block-input-preview">
                <?= $form->field($baner, 'src')
                    ->textInput(['id' => 'cover','style' => 'display:none', 'class' => 'input-field-setting',
                        'placeholder' => 'Введите текст', 'value' => $baner['src']])
                    ->label(false) ?>
            </div>
        </div>
        <input style="display: none" class="photo-preview-add" name="photo-add" type="file"
               accept="image/*,image/jpeg,image/gif,image/png">

        <label>
            <div class="btn-send" style="z-index: 3;position: relative">
                <div class="large-wide-button"><p>Опубликовать</p></div>
            </div>
            <input id="btn-form-save" type="submit" style="display: none;">
        </label>

    </div>


    <?php ActiveForm::end();?>
</div>


<?php

if(isset($toastMessage)) {
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

Pjax::end();