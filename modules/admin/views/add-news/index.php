<?php
use \yii\widgets\ActiveForm;
$this->title = 'Добавить новость на Postim.by';
?>
<div class="margin-top60"></div>
<div class="block-content">
    <h1 class="h1-c" style="margin-top: 35px">Добавить новость</h1>
    <?php $form = ActiveForm::begin(['id' => 'form-add-news', 'enableClientScript' => false,'action'=>'/admin/add-news/save','options'=>['pjax-container-add-news'=>'true']]) ?>

    <div class="container-add-place container-feedback" style="margin-top: 30px">

        <div class="block-field-setting">
            <label class="label-field-setting">Заголовок страницы</label>
            <?= $form->field($news, 'header')
                ->textInput(['style' => 'margin-bottom: 15px;', 'class' => 'input-field-setting',
                    'placeholder' => 'Введите текст', 'value' => $news['header']])
                ->label(false) ?>
        </div>

    </div>

    <div class="container-add-place" style="margin-top: 30px">
        <div class="block-field-setting" style="border-bottom: 0px">
            <label class="label-field-setting">Адрес</label>
            <div class="selectorFields" data-is-many="false" data-id="News[city_id]" data-max="1"
                 data-info='<?= \yii\helpers\Json::encode($cities) ?>'>
                <div class="block-inputs"></div>
                <div class="between-selected-field btn-open-field" data-open=false>
                    <input class="search-selected-field" type="button" data-value="Выберите город"
                           value="Выберите город" placeholder="Выберите город">
                    <div class="open-select-field2"></div>
                </div>
                <div class="container-scroll-fields">
                    <div class="container-options"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-add-place">
        <div class="container-description">
            <div class="description-header">Описание</div>
            <div class="block-write-editors">

                <?= $form->field($news, 'data')
                    ->textInput(['id' => 'article','style' => 'display:none', 'class' => 'input-field-setting',
                        'placeholder' => 'Введите текст', 'value' => \yii\helpers\Html::encode($news['data'])])
                    ->label(false) ?>

                <?php if($news['data']):?>
                    <?=\app\components\Helper::parserForEditor($news['data'],true);?>
                <?php else:?>
                    <div class="item item-editor-default container-editor"><div class="editable"></div></div>
                <?php endif;?>
            </div>
        </div>

        <script>
            $(document).ready(function () {
                editable.init('.editable', {
                    toolbar: {
                        video: true,
                        text: true
                    }
                })
            });
        </script>

    </div>

    <div class="container-add-place">
        <div class="container-gallery">
            <div class="gallery-header">Превью новости</div>
            <div class="block-gallery">

            </div>
            <div class="btn-add-photo-preview">Добавить фотографии</div>
            <div class="block-input-preview">
                <?= $form->field($news, 'cover')
                    ->textInput(['id' => 'cover','style' => 'display:none', 'class' => 'input-field-setting',
                        'placeholder' => 'Введите текст', 'value' => $news['cover']])
                    ->label(false) ?>
            </div>
        </div>
        <input style="display: none" class="photo-preview-add" name="photo-add" type="file"
               accept="image/*,image/jpeg,image/gif,image/png">
    </div>

    <div class="container-add-place container-feedback" style="margin-top: 30px">
        <div class="block-field-setting">
            <label class="label-field-setting">Описание для превью</label>
            <?= $form->field($news, 'description')
                ->textInput(['style' => 'margin-bottom: 15px;', 'class' => 'input-field-setting',
                    'placeholder' => 'Введите текст', 'value' => $news['description']])
                ->label(false) ?>
        </div>
    </div>

    <div class="container-add-place container-feedback" style="margin-top: 30px">

        <div class="block-field-setting">
            <label class="label-field-setting">Заголов для поисковиков</label>
            <?= $form->field($news, 'title_s')
                ->textInput(['style' => 'margin-bottom: 15px;', 'class' => 'input-field-setting',
                    'placeholder' => 'Введите текст', 'value' => $news['title_s']])
                ->label(false) ?>
        </div>

        <div class="block-field-setting">
            <label class="label-field-setting">Описание для поисковиков</label>
            <?= $form->field($news, 'description_s')
                ->textInput(['style' => 'margin-bottom: 15px;', 'class' => 'input-field-setting',
                    'placeholder' => 'Введите текст', 'value' => $news['description_s']])
                ->label(false) ?>
        </div>

        <div class="block-field-setting">
            <label class="label-field-setting">Ключевые слова</label>
            <?= $form->field($news, 'key_word_s')
                ->textInput(['style' => 'margin-bottom: 15px;', 'class' => 'input-field-setting',
                    'placeholder' => 'Введите текст', 'value' => $news['key_word_s']])
                ->label(false) ?>
        </div>

        <div class="btn-send js-btn-save" style="z-index: 3;position: relative">
            <div class="large-wide-button"><p>Опубликовать</p></div>
        </div>
        <input id="btn-form-save" type="submit" style="display: none;">
        <script>
            $(document).ready(function () {
                $('.js-btn-save').click(function () {
                    if(editable.parserEditable()){
                        $('#btn-form-save').trigger('click');
                    }
                })
            });
        </script>

    </div>

    <?php ActiveForm::end()?>

</div>
<div style="margin-bottom:30px;"></div>
