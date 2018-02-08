<?php

use yii\helpers\Url;

$this->title = 'Добавить скидку на Postim.by';
?>

<div class="margin-top60"></div>
<div class="block-content">
    <form action="<?=Url::to(['discount/add', 'postId' => $model->post_id]);?>" id="discount-form" method="post">
        <div class="container-add-place">
            <div class="block-field-setting">
                <label class="label-field-setting">Название скидки</label>
                <input name="discount[header]" class="input-field-setting"
                       placeholder="Введите название">
            </div>

            <div class="block-field-setting">
                <label class="label-field-setting">Категория</label>
                <div class="selected-field">
                    <div id="select-category-value" class="select-value" data-value="1">
                        Промокод
                    </div>
                    <div data-open-id="select-category" class="open-select-field"></div>
                </div>
                <div id="select-category" class="container-scroll auto-height">
                    <div class="container-option-select option-active"></div>
                </div>
                <input type="hidden" id="select-category-hidden" name="discount[type]" value="1">

            </div>
        </div>

        <div class="container-add-place">
            <div class="block-field-setting">
                <label class="label-field-setting">Дата окончания акции</label>

                <div class="selected-field" style="margin-bottom: 20px;">
                    <div class="select-value">
                        <span id="end-share-date" class="placeholder-select">Укажите дату</span>

                    </div>
                    <div data-open-id="select-worktime" class="close-select-field"></div>
                </div>
                <div id="select-worktime" style="margin-top: 0px;" class="open-select">
                    <div id="datepicker" style="margin-bottom: 20px;"></div>
                    <input id="date_finish" name="discount[date_finish]" type="hidden">
                </div>

            </div>
        </div>

        <div class="container-add-place">
            <div class="block-field-setting">
                <label class="label-field-setting">Условия акции</label>

                <div class="option-select-field">
                    <div class="block-textarea-custom">
                            <textarea data-preview-text="Необходимо предъявлять промокод до заказа"
                                      data-continue-text=". Скидка предоставляется только при наличии неиспользованного ранее промокода, вы можете его назвать по телефону, предъявить в распечатанном или в электронном виде."
                                      placeholder="Укажите условие"
                                      name="discount[conditions][]"
                            >Необходимо предъявлять промокод до заказа. Скидка предоставляется только при наличии неиспользованного ранее промокода, вы можете его назвать по телефону, предъявить в распечатанном или в электронном виде.</textarea>
                        <div class="close-input-custom" ></div>
                    </div>
                </div>
                <div class="option-select-field">
                    <div class="block-textarea-custom">
                            <textarea data-preview-text="Обязателен предварительный заказ"
                                      data-continue-text=" или бронь по телефонам, указанным в купоне на скидку."
                                      placeholder="Укажите условие"
                                      name="discount[conditions][]"
                            >Обязателен предварительный заказ или бронь по телефонам, указанным в купоне на скидку.</textarea>
                        <div class="close-input-custom" ></div>
                    </div>
                </div>
                <div class="option-select-field">
                    <div class="block-textarea-custom">
                            <textarea data-preview-text="Скидка по промокоду не суммируется"
                                      data-continue-text=" с другими акциями и спецпредложениями."
                                      placeholder="Укажите условие"
                                      name="discount[conditions][]"
                            >Скидка по промокоду не суммируется с другими акциями и спецпредложениями.</textarea>
                        <div class="close-input-custom" ></div>
                    </div>
                </div>

                <div class="selected-field" id="add-share-condition">
                    <div id="select-condition-value" class="select-value">
                        <span class="placeholder-select">Добавить условие</span>
                    </div>
                    <div data-open-id="select-condition" class="open-select-field"></div>
                </div>

                <div id="select-condition" class="container-scroll auto-height" style="max-height: none;">
                    <div class="option-select-field another-condition">
                        <div class="block-textarea-custom hidden">
                            <textarea data-preview-text=""
                                      data-continue-text=""
                                      placeholder="Укажите условие"
                                      readonly>Другие условия</textarea>
                            <div class="close-input-custom" ></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="block-field-setting">
                <label class="label-field-setting">Реквизиты</label>
                <input  name="discount[requisites]" class="input-field-setting"
                        placeholder="Введите реквизиты: ООО, УНП и т.д."
                        value="<?= !empty($post->requisites) ? $post->requisites : ''?>">
            </div>
        </div>

        <div class="container-add-place">
            <div class="block-field-setting">
                <label class="label-field-setting">Стоимость товара или услуги (руб)</label>
                <input id="price" name="discount[price]" class="input-field-setting"
                       placeholder="Укажите цену, если это возможно" value="">
            </div>

            <div class="block-field-setting">
                <label class="label-field-setting">Скидка (%)</label>
                <input id="discount" name="discount[discount]" class="input-field-setting"
                       placeholder="Укажите скидку, если это возможно" value="">
            </div>

            <div class="block-field-setting">
                <label class="label-field-setting">Экономия</label>
                <input id="economy" class="input-field-setting"
                       placeholder="&#8734;" value="" readonly>
            </div>
            <div class="block-field-setting">
                <label class="label-field-setting">Цена со скидкой</label>
                <input id="price-with-discount" name="discount[price_with_discount]" class="input-field-setting"
                       placeholder="Укажите цену со скидкой, если это возможно" value="">
            </div>
            <div class="block-field-setting">
                <label class="label-field-setting">Количество промокодов</label>
                <input id="product-count" name="discount[number_purchases]" class="input-field-setting"
                       placeholder="Укажите количество" value="">
            </div>
            <div class="block-field-setting">
                <label class="label-field-setting">Название промокода</label>
                <input id="product-count" name="discount[promocode]" class="input-field-setting"
                       placeholder="Укажите единый промокод или мы сгенерируем уникальный промокод для каждого клиента" value="">
            </div>
        </div>

        <div class="container-add-place">
            <div class="container-description">
                <div class="description-header">Описание акции</div>
                <div class="block-write-editors">
                    <input id="article" name="discount[data]" type="text" style="display: none"
                           data-upload-by-url="/discount/upload-new-photo-by-url?postId=<?=$model->post_id?>"
                           data-upload-by-file="/discount/upload-new-photo?postId=<?=$model->post_id?>">
                    <div class="item item-editor-default container-editor">
                        <div class="editable"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-add-place">
            <div class="container-gallery">
                <div class="gallery-header">Галерея</div>
                <div class="block-inputs-gallery" style="display: none"></div>
                <div class="block-gallery"></div>

                <label class="btn-add-photo-preview" for="discount-gallery">Добавить фотографии</label>
            </div>
            <input style="display: none" id="discount-gallery" name="discount-gallery" type="file" multiple
                   accept="image/*,image/jpeg,image/gif,image/png">
        </div>

        <div class="container-add-place">
            <?php if(Yii::$app->user->isModerator()):?>
                <div class="block-field-setting">
                    <label class="label-field-setting">Заголовок для поисковиков</label>
                    <input name="discount[title]" class="input-field-setting" placeholder="Введите текст" value="">
                </div>
                <div class="block-field-setting">
                    <label class="label-field-setting">Описание для поисковиков</label>
                    <input name="discount[description]" class="input-field-setting" placeholder="Введите текст" value="">
                </div>
                <div class="block-field-setting">
                    <label class="label-field-setting">Ключевые слова</label>
                    <input name="discount[key_word]" class="input-field-setting" placeholder="Введите текст" value="">
                </div>
            <?php endif;?>
            <div class="btn-send">
                <div id="add-discount" class="large-wide-button"><p>Опубликовать</p></div>
            </div>
        </div>


    </form>
</div>
<div style="margin-bottom:30px;"></div>

<script>
    $(document).ready(function () {
        $('.block-inputs').sortable();

        editable.init('.editable', {
            toolbar: {
                photo: true,
                video: true,
                text: true
            }
        });
        $('textarea').autosize();
        $("#datepicker").datepicker({
            minDate: 0,
            onSelect: function(dateText, inst) {
                var date = $(this).datepicker("getDate");

                $('#end-share-date').text(dateText);
                $('#date_finish').val(Math.round(date.getTime()/1000));
            }
        });

        $('#price').mask("###0.00", {reverse: true});
        $('#price-with-discount').mask("###0.00", {reverse: true});
        $('#discount').mask("#0", {reverse: true});
        $('#product-count').mask("#0", {reverse: true});
    })
</script>