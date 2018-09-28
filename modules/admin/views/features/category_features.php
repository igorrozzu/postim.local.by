<?php

use \yii\widgets\ActiveForm;

$this->title = 'Добавить особенность в категории';

use yii\widgets\Pjax;


Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => false,
    'id' => 'pjax-container',
    'linkSelector' => false,
    'formSelector' => '#pjax-container form',
]);

?>

<div class="margin-top60"></div>
<div class="block-content">

    <h1 class="h1-c" style="margin-top: 35px">Добавить особенность в категорию</h1>
    <?php $form = ActiveForm::begin([
        'id' => 'form-add',
        'enableClientScript' => false,
        'action' => '/admin/features/bind-category-and-features',
        'options' => ['pjax-container' => 'true'],
    ]) ?>

    <div class="container-add-place" style="margin-top: 30px">

        <div class="block-field-setting" style="border-bottom: 0px">
            <label class="label-field-setting">Особенность</label>
            <div class="selectorFields" data-is-many="false" data-id="CategoryFeatures[features_id]" data-max="1"
                 data-info='<?= \yii\helpers\Json::encode($model->getFeaturesList()) ?>'>
                <div class="block-inputs" style="display: none">
                    <div class="btn-selected-option"><span class="option-text"><?= $model->features_id ?></span> <span
                                class="close-selected-option"></span> <input name="CategoryFeatures[features_id]"
                                                                             value="<?= $model->features_id ?>"
                                                                             style="display: none"></div>
                </div>
                <div class="between-selected-field btn-open-field" data-open=false>
                    <input class="search-selected-field" type="button" data-value="<?= $model->getLabelFeatures() ?>"
                           value="<?= $model->getLabelFeatures() ?>" placeholder="Поиск">
                    <div class="open-select-field2"></div>
                </div>
                <div class="container-scroll-fields">
                    <div class="container-options"></div>
                </div>
            </div>
        </div>
        <div class="block-field-setting" style="border-bottom: 0px">
            <label class="label-field-setting">Категория</label>
            <div class="selectorFields" data-is-many="false" data-id="CategoryFeatures[category_id]" data-max="1"
                 data-info='<?= \yii\helpers\Json::encode($model->getCategoriesList()) ?>'>
                <div class="block-inputs" style="display: none">
                    <div class="btn-selected-option"><span class="option-text"><?= $model->category_id ?></span> <span
                                class="close-selected-option"></span> <input name="CategoryFeatures[category_id]"
                                                                             value="<?= $model->category_id ?>"
                                                                             style="display: none"></div>
                </div>
                <div class="between-selected-field btn-open-field" data-open=false>
                    <input class="search-selected-field" type="button" data-value="<?= $model->getLabelCategories() ?>"
                           value="<?= $model->getLabelCategories() ?>" placeholder="Поиск">
                    <div class="open-select-field2"></div>
                </div>
                <div class="container-scroll-fields">
                    <div class="container-options"></div>
                </div>
            </div>
        </div>

        <label>
            <div class="btn-send" style="z-index: 3;position: relative">
                <div class="large-wide-button"><p>Добавить</p></div>
            </div>
            <input id="btn-form-edit-page" type="submit" style="display: none;">
        </label>

    </div>


    <?php
    ActiveForm::end();
    ?>


    <h1 class="h1-c" style="margin-top: 100px">Добавить особенность в подкатегорию</h1>
    <?php $form = ActiveForm::begin([
        'id' => 'form-add2',
        'enableClientScript' => false,
        'action' => '/admin/features/bind-category-and-features',
        'options' => ['pjax-container' => 'true'],
    ]) ?>

    <div class="container-add-place" style="margin-top: 30px">

        <div class="block-field-setting" style="border-bottom: 0px">
            <label class="label-field-setting">Особенность</label>
            <div class="selectorFields" data-is-many="false" data-id="UnderCategoryFeatures[features_id]" data-max="1"
                 data-info='<?= \yii\helpers\Json::encode($model->getFeaturesList()) ?>'>
                <div class="block-inputs" style="display: none">
                    <div class="btn-selected-option"><span class="option-text"><?= $modelUnder->features_id ?></span>
                        <span class="close-selected-option"></span> <input name="UnderCategoryFeatures[features_id]"
                                                                           value="<?= $modelUnder->features_id ?>"
                                                                           style="display: none"></div>
                </div>
                <div class="between-selected-field btn-open-field" data-open=false>
                    <input class="search-selected-field" type="button" data-value="<?= $model->getLabelFeatures() ?>"
                           value="<?= $modelUnder->getLabelFeatures() ?>" placeholder="Поиск">
                    <div class="open-select-field2"></div>
                </div>
                <div class="container-scroll-fields">
                    <div class="container-options"></div>
                </div>
            </div>
        </div>
        <div class="block-field-setting" style="border-bottom: 0px">
            <label class="label-field-setting">Подкатегория</label>
            <div class="selectorFields" data-is-many="false" data-id="UnderCategoryFeatures[under_category_id]"
                 data-max="1"
                 data-info='<?= \yii\helpers\Json::encode($modelUnder->getCategoriesList()) ?>'>
                <div class="block-inputs" style="display: none">
                    <div class="btn-selected-option"><span
                                class="option-text"><?= $modelUnder->under_category_id ?></span> <span
                                class="close-selected-option"></span> <input
                                name="UnderCategoryFeatures[under_category_id]"
                                value="<?= $modelUnder->under_category_id ?>" style="display: none"></div>
                </div>
                <div class="between-selected-field btn-open-field" data-open=false>
                    <input class="search-selected-field" type="button"
                           data-value="<?= $modelUnder->getLabelCategories() ?>"
                           value="<?= $modelUnder->getLabelCategories() ?>" placeholder="Поиск">
                    <div class="open-select-field2"></div>
                </div>
                <div class="container-scroll-fields">
                    <div class="container-options"></div>
                </div>
            </div>
        </div>

        <label>
            <div class="btn-send" style="z-index: 3;position: relative">
                <div class="large-wide-button"><p>Добавить</p></div>
            </div>
            <input id="btn-form-edit-page" type="submit" style="display: none;">
        </label>

    </div>

    <?php
    ActiveForm::end();
    ?>
</div>


<?php

if (isset($toastMessage)) {
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
