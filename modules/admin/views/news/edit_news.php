<?php
use \yii\widgets\ActiveForm;
use yii\widgets\Pjax;

$this->title = 'Редактирование новости на Postim.by';

Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => false,
    'id' => 'pjax-container-add-news',
    'linkSelector' => false,
    'formSelector' => '#pjax-container-add-news form',
]);

?>
<div class="margin-top60"></div>
<div class="block-content">
    <h1 class="h1-c" style="margin-top: 35px">Редактирование новости</h1>
    <?php $form = ActiveForm::begin(['id' => 'form-add-news', 'enableClientScript' => false,'action'=>'/admin/news/save','options'=>['pjax-container-add-news'=>'true']]) ?>
    <?php if($news->id):?>
        <div style="display: none">
            <?= $form->field($news, 'id')
                ->textInput(['style' => 'margin-bottom: 15px;', 'class' => 'input-field-setting',
                    'placeholder' => 'id', 'value' => $news['id']])
                ->label(false) ?>
        </div>
    <?php endif;?>

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
                <div class="block-inputs" style="display: none">
                    <?php if($news->city_id):?>
                        <div class="btn-selected-option"><span class="option-text"><?=$news->city->name?></span> <span class="close-selected-option"></span> <input name="News[city_id]" value="<?=$news->city->id?>" style="display: none"> </div>
                    <?php endif;?>
                </div>
                <div class="between-selected-field btn-open-field" data-open=false>
                    <input class="search-selected-field" type="button" data-value="Выберите город"
                           value="<?=$news->city_id?$news->city->name:'Выберите город'?>" placeholder="Выберите город">
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
            <div class="description-header">Редактировать</div>
            <div class="block-write-editors">

                <?php if($news['data']):?>
                    <?=\app\components\Helper::parserForEditor($news['data'],false);?>
                <?php else:?>
                    <div class="item-editor item container-editor"><div class="container-toolbar"> <div class="title-toolbar">Текст</div> <div class="btns-toolbar-container"><div class="btn-toolbar-top"></div> <div class="btn-toolbar-down"></div> <div class="btn-toolbar-close"></div></div></div><div class="editable"></div></div>
                <?php endif;?>
            </div>
            <?= $form->field($news, 'data')
                ->textInput(['id' => 'article','style' => 'display:none', 'class' => 'input-field-setting',
                    'placeholder' => 'Введите текст', 'value' => \yii\helpers\Html::encode($news['data']),
                    'data-upload-by-url' => '/post/upload-new-photo-by-url',
                    'data-upload-by-file' => '/post/upload-new-photo',
                ])->label(false) ?>
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

    </div>

    <div class="container-add-place">
        <div class="container-gallery">
            <div class="gallery-header">Превью новости</div>
            <div class="block-gallery">
                <?php if($news->cover):?>
                    <div id="-1771374919" class="item-photo-from-gallery" style="background-image: url('<?=$news->getPatchCover()?>');"> <div class="container-blackout"> <div class="header-btns">    <span class="btn-item-photo btn-close-photo-preview"></span> </div>  </div> </div>
                <?php endif;?>
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
            <label class="label-field-setting">Название для превью</label>
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
?>
