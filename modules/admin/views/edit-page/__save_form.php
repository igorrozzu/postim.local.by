<?php
use \yii\widgets\ActiveForm;
use yii\widgets\Pjax;

Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => false,
    'id' => 'pjax-container-edit-page',
    'linkSelector' => false,
    'formSelector' => '#pjax-container-edit-page form',
]);
?>

<div class="margin-top60"></div>
<div class="block-content">
    <h1 class="h1-c" style="margin-top: 35px">Редактирование</h1>
    <div class="container-add-place container-feedback" style="margin-top: 30px">

        <?php $form = ActiveForm::begin(['id' => 'form-edit-page', 'enableClientScript' => false,'action'=>'/admin/edit-page','options'=>['pjax-container-edit-page'=>'true']]) ?>
        <div class="block-field-setting">
            <label class="label-field-setting">URL страницы</label>
            <?= $form->field($editPage, 'url_page')
                ->textInput(['style' => 'margin-bottom: 15px;', 'class' => 'input-field-setting',
                    'placeholder' => 'Вставьте адрес редактируемой страницы', 'value' => $editPage['find_url']])
                ->label(false) ?>
        </div>

        <label>
            <div class="btn-send" style="z-index: 3;position: relative">
                <div class="large-wide-button"><p>Редактировать</p></div>
            </div>
            <input id="btn-form-edit-page" type="submit" style="display: none;">
        </label>
        <?php ActiveForm::end()?>
    </div>

    <?php $form = ActiveForm::begin(['id' => 'form-edit-page-save', 'enableClientScript' => false,'action'=>'/admin/edit-page/save','options'=>['pjax-container-edit-page'=>'true']]) ?>
    <div class="container-add-place container-feedback" style="margin-top: 30px">

        <div class="block-field-setting" style="display: none">
            <label class="label-field-setting">URL страницы</label>
            <?= $form->field($editPage, 'url_page')
                ->textInput(['style' => 'margin-bottom: 15px;', 'class' => 'input-field-setting',
                    'placeholder' => 'Вставьте адрес редактируемой страницы', 'value' => $editPage['url_page']])
                ->label(false) ?>
        </div>

        <div class="block-field-setting">
            <label class="label-field-setting">Заголовок страницы</label>
            <?= $form->field($editPage, 'h1')
                ->textInput(['style' => 'margin-bottom: 15px;', 'class' => 'input-field-setting',
                    'placeholder' => 'Введите текст', 'value' => $editPage['h1']])
                ->label(false) ?>
        </div>

        <div class="block-field-setting">
            <label class="label-field-setting">Заголов для поисковиков</label>
            <?= $form->field($editPage, 'title')
                ->textInput(['style' => 'margin-bottom: 15px;', 'class' => 'input-field-setting',
                    'placeholder' => 'Введите текст', 'value' => $editPage['title']])
                ->label(false) ?>
        </div>

        <div class="block-field-setting">
            <label class="label-field-setting">Описание для поисковиков</label>
            <?= $form->field($editPage, 'description')
                ->textInput(['style' => 'margin-bottom: 15px;', 'class' => 'input-field-setting',
                    'placeholder' => 'Введите текст', 'value' => $editPage['description']])
                ->label(false) ?>
        </div>

        <div class="block-field-setting">
            <label class="label-field-setting">Ключевые слова</label>
            <?= $form->field($editPage, 'key_word')
                ->textInput(['style' => 'margin-bottom: 15px;', 'class' => 'input-field-setting',
                    'placeholder' => 'Введите текст', 'value' => $editPage['key_word']])
                ->label(false) ?>
        </div>

    </div>
    <div class="container-add-place">
        <div class="container-description">
            <div class="description-header">Описание</div>
            <div class="block-write-editors">

                <?= $form->field($editPage, 'description_text')
                    ->textInput(['id' => 'article','style' => 'display:none', 'class' => 'input-field-setting',
                        'placeholder' => 'Введите текст', 'value' => \yii\helpers\Html::encode($editPage['description_text']),
                        'data-upload-by-url' => '/post/upload-new-photo-by-url',
                        'data-upload-by-file' => '/post/upload-new-photo',
                    ])->label(false) ?>

                <?php if($editPage['description_text']):?>
                    <?=\app\components\Helper::parserForEditor($editPage['description_text'],true);?>
                <?php else:?>
                    <div class="item item-editor-default container-editor"><div class="editable"></div></div>
                <?php endif;?>
            </div>
        </div>

        <script>
            $(document).ready(function () {
                editable.init('.editable', {
                    toolbar: {
                        photo: true,
                        video: true,
                        text: true

                    }
                })
            });
        </script>

        <div class="btn-send js-btn-save" style="z-index: 3;position: relative">
            <div class="large-wide-button"><p>Опубликовать</p></div>
        </div>
        <input id="btn-form-save" type="submit" style="display: none;">
        <script>
            $(document).ready(function () {
                $('.js-btn-save').click(function () {
                    if(editable.parserEditable()){
                        console.log('1212');
                        $('#btn-form-save').trigger('click');
                    }
                })
            });
        </script>
        <?php ActiveForm::end()?>
    </div>
<div class="margin-top60"></div>
</div>
<?php
Pjax::end();
?>