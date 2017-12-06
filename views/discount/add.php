<?php

$this->title = 'Добавить скидку на Postim.by';
?>
<div class="margin-top60"></div>
<div class="block-content">
    <form action="#" id="discount-form" method="post">
        <div class="container-add-place">
            <div class="block-field-setting">
                <label class="label-field-setting">Название скидки</label>
                <input name="name" class="input-field-setting validator" data-error-parents="block-field-setting"
                       data-message="Введите название" data-regex="^.+$" placeholder="Введите название" value="">
            </div>

            <div class="block-field-setting">
                <label class="label-field-setting">Категория</label>
                <div class="selected-field">
                    <div id="select-category-value" class="select-value" data-value="">
                        <span class="placeholder-select">Выберите категорию</span>
                    </div>
                    <div data-open-id="select-category" class="open-select-field"></div>
                </div>
                <div id="select-category" class="container-scroll auto-height">
                    <div class="container-option-select option-active">
                        <div data-value="1" class="option-select-field">Промокод</div>
                        <div data-value="2" class="option-select-field">Сертификат</div>
                    </div>
                </div>
                <input type="hidden" id="select-category-hidden" name="category" value="0">

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
                </div>

            </div>
        </div>

        <div class="container-add-place">
            <div class="block-field-setting">
                <label class="label-field-setting">Условия акции</label>

                <div class="selected-field" id="add-share-condition">
                    <div id="select-condition-value" class="select-value">
                        <span class="placeholder-select">Добавить условие</span>
                    </div>
                    <div data-open-id="select-condition" class="open-select-field"></div>
                </div>
                <div id="select-condition" class="container-scroll auto-height" style="max-height: none;">

                    <div class="option-select-field">
                        <div class="block-textarea-custom hidden">
                            <textarea class="validator"
                                   data-error-parents="block-textarea-custom"
                                   data-message="Неккоректные данные для условия"
                                   data-preview-text="Воспользоваться промокодом вы можете до"
                                   data-continue-text=" 00.00.2018."
                                   placeholder="Укажите условие"
                                   readonly>Воспользоваться промокодом вы можете до</textarea>
                            <div class="close-input-custom" ></div>
                        </div>
                    </div>
                    <div class="option-select-field">
                        <div class="block-textarea-custom hidden">
                            <textarea class="validator"
                                      data-error-parents="block-textarea-custom"
                                      data-message="Неккоректные данные для условия"
                                      data-preview-text="Промокод действует на одного человека"
                                      data-continue-text=". Если идете компанией, необходимо приобретать промокоды на каждого."
                                      placeholder="Укажите условие"
                                      readonly>Промокод действует на одного человека</textarea>
                            <div class="close-input-custom" ></div>
                        </div>
                    </div>
                    <div class="option-select-field">
                        <div class="block-textarea-custom hidden">
                            <textarea class="validator"
                                      data-error-parents="block-textarea-custom"
                                      data-message="Неккоректные данные для условия"
                                      data-preview-text="Необходимо предъявлять промокод до заказа"
                                      data-continue-text=". Скидка предоставляется только при наличии неиспользованного ранее промокода, вы можете его назвать по телефону, предъявить в распечатанном или в электронном виде."
                                      placeholder="Укажите условие"
                                      readonly>Необходимо предъявлять промокод до заказа</textarea>
                            <div class="close-input-custom" ></div>
                        </div>
                    </div>
                    <div class="option-select-field">
                        <div class="block-textarea-custom hidden">
                            <textarea class="validator"
                                      data-error-parents="block-textarea-custom"
                                      data-message="Неккоректные данные для условия"
                                      data-preview-text="Обязателен предварительный заказ"
                                      data-continue-text=" или бронь по телефонам, указанным в купоне на скидку."
                                      placeholder="Укажите условие"
                                      readonly>Обязателен предварительный заказ</textarea>
                            <div class="close-input-custom" ></div>
                        </div>
                    </div>
                    <div class="option-select-field">
                        <div class="block-textarea-custom hidden">
                            <textarea class="validator"
                                      data-error-parents="block-textarea-custom"
                                      data-message="Неккоректные данные для условия"
                                      data-preview-text="Скидка по промокоду не суммируется"
                                      data-continue-text=" с другими акциями и спецпредложениями."
                                      placeholder="Укажите условие"
                                      readonly>Скидка по промокоду не суммируется</textarea>
                            <div class="close-input-custom" ></div>
                        </div>
                    </div>
                    <div class="option-select-field">
                        <div class="block-textarea-custom hidden">
                            <textarea class="validator"
                                      data-error-parents="block-textarea-custom"
                                      data-message="Неккоректные данные для условия"
                                      data-preview-text="Поставщик несет полную ответственность"
                                      data-continue-text=" перед потребителем за достоверность информации."
                                      placeholder="Укажите условие"
                                      readonly>Поставщик несет полную ответственность</textarea>
                            <div class="close-input-custom" ></div>
                        </div>
                    </div>
                    <div class="option-select-field">
                        <div class="block-textarea-custom hidden">
                            <textarea class="validator"
                                      data-error-parents="block-textarea-custom"
                                      data-message="Неккоректные данные для условия"
                                      data-preview-text='Услуги (товары) предоставляются ООО "Рестгорсервис" УНП 191206305.'
                                      data-continue-text=""
                                      placeholder="Укажите условие"
                                      readonly>Услуги (товары) предоставляются ООО "Рестгорсервис" УНП 191206305.</textarea>
                            <div class="close-input-custom" ></div>
                        </div>
                    </div>
                    <div class="option-select-field another-condition">
                        <div class="block-textarea-custom hidden">
                            <textarea class="validator"
                                      data-error-parents="block-textarea-custom"
                                      data-message="Неккоректные данные для условия"
                                      data-preview-text=""
                                      data-continue-text=""
                                      placeholder="Укажите условие"
                                      readonly>Другие условия</textarea>
                            <div class="close-input-custom" ></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-add-place">
            <div class="block-field-setting">
                <label class="label-field-setting">Стоимость товара или услуги (руб)</label>
                <input name="cost" class="input-field-setting validator"
                       data-error-parents="block-field-setting"
                       data-message="Введите название"
                       placeholder="Укажите цену, если это возможно" value="">
            </div>

            <div class="block-field-setting">
                <label class="label-field-setting">Скидка (%)</label>
                <input name="discount" class="input-field-setting validator"
                       data-error-parents="block-field-setting"
                       data-message="Укажите скидку"
                       placeholder="Укажите скидку" value="">
            </div>

            <div class="block-field-setting">
                <label class="label-field-setting">Экономия</label>
                <input class="input-field-setting validator"
                       data-error-parents="block-field-setting"
                       placeholder="~" value="" readonly>
            </div>
            <div class="block-field-setting">
                <label class="label-field-setting">Цена со скидкой</label>
                <input class="input-field-setting validator"
                       data-error-parents="block-field-setting"
                       placeholder="~" value="" readonly>
            </div>
            <div class="block-field-setting">
                <label class="label-field-setting">Колличество промокодов или сертификатов</label>
                <input name="product-count" class="input-field-setting validator"
                       data-error-parents="block-field-setting"
                       placeholder="Укажите колличество" value="">
            </div>
        </div>

        <div class="container-add-place">
            <div class="container-description">
                <div class="description-header">Описание акции</div>
                <div class="block-write-editors">
                    <input id="article" name="promotion" type="text" style="display: none">
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

                <div class="btn-add-photos-gallery">Добавить фотографии</div>
            </div>
            <input style="display: none" class="photo-add" name="photo-add" type="file" multiple
                   accept="image/*,image/jpeg,image/gif,image/png">
        </div>

        <div class="container-add-place">
            <?php if(Yii::$app->user->identity->role > 1):?>
                <div class="block-field-setting">
                    <label class="label-field-setting">Заголовок для поисковиков</label>
                    <input name="engine[title]" class="input-field-setting" placeholder="Введите текст" value="">
                </div>
                <div class="block-field-setting">
                    <label class="label-field-setting">Описание для поисковиков</label>
                    <input name="engine[description]" class="input-field-setting" placeholder="Введите текст" value="">
                </div>
                <div class="block-field-setting">
                    <label class="label-field-setting">Ключевые слова</label>
                    <input name="engine[key_word]" class="input-field-setting" placeholder="Введите текст" value="">
                </div>
                <div class="btn-place-save">
                    <div class="large-wide-button"><p>Опубликовать</p></div>
                </div>
            <?php else:?>
                <div class="btn-place-save" style="margin-top: -20px">
                    <div class="large-wide-button"><p>На модерацию</p></div>
                </div>
            <?php endif;?>
        </div>


    </form>
</div>
<div style="margin-bottom:30px;"></div>

<script>
    $(document).ready(function () {
        $('.block-inputs').sortable();

        editable.init('.editable', {
            toolbar: {
                video: true,
                text: true
            }
        });
        $('textarea').autosize();
        $("#datepicker").datepicker({
            onSelect: function(dateText, inst) {
                $('#end-share-date').text(dateText);
                console.log(inst);
            }
        });
    })
</script>