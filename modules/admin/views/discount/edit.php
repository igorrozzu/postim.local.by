<?php
use yii\helpers\Json;
use app\components\Helper;

$this->title = 'Редактировать скидку на Postim.by';
?>

<div class="margin-top60"></div>
<div class="block-content">
    <form action="" id="edit-discount-form" method="post">
        <div class="container-add-place">
            <div class="block-field-setting">
                <label class="label-field-setting">Название скидки</label>
                <input name="discount[header]" class="input-field-setting"
                        placeholder="Введите название" value="<?=$discount->header?>">
            </div>

            <div class="block-field-setting">
                <label class="label-field-setting">Категория</label>
                <div class="selected-field">
                    <div id="select-category-value" class="select-value" data-value="<?=$discount->type?>">
                        <?=$discount->getNameType()?>
                    </div>
                    <div data-open-id="select-category" class="open-select-field"></div>
                </div>
                <div id="select-category" class="container-scroll auto-height">
                    <div class="container-option-select option-active"></div>
                </div>
                <input type="hidden" id="select-category-hidden" name="discount[type]" value="<?=$discount->type?>">

            </div>
        </div>

        <div class="container-add-place">
            <div class="block-field-setting">
                <label class="label-field-setting">Дата окончания акции</label>

                <div class="selected-field" style="margin-bottom: 20px;">
                    <div class="select-value">
                        <span id="end-share-date" class="placeholder-select">
                            <?=Yii::$app->formatter->asDate(
                                $discount->date_finish + Yii::$app->user->identity->getTimezoneInSeconds(),
                                'dd.MM.yyyy');?>
                        </span>

                    </div>
                    <div data-open-id="select-worktime" class="close-select-field"></div>
                </div>
                <div id="select-worktime" style="margin-top: 0px;" class="open-select">
                    <div id="datepicker" style="margin-bottom: 20px;"></div>
                    <input id="date_finish" name="discount[date_finish]" type="hidden" value="<?=$discount->date_finish?>">
                </div>

            </div>
        </div>

        <div class="container-add-place">
            <div class="block-field-setting">
                <label class="label-field-setting">Условия акции</label>

                <?php foreach (Json::decode($discount->conditions) as $condition): ?>
                    <div class="option-select-field another-condition">
                        <div class="block-textarea-custom">
                                <textarea placeholder="Укажите условие"
                                          name="discount[conditions][]"
                                ><?=$condition?></textarea>
                            <div class="close-input-custom" ></div>
                        </div>
                    </div>
                <?php endforeach;?>

                <div class="selected-field" id="add-share-condition">
                    <div id="select-condition-value" class="select-value">
                        <span class="placeholder-select">Добавить условие</span>
                    </div>
                    <div data-open-id="select-condition" class="open-select-field"></div>
                </div>

                <div id="select-condition" class="container-scroll auto-height" style="max-height: none;">
                    <div class="option-select-field">
                        <div class="block-textarea-custom hidden">
                            <textarea data-preview-text="Воспользоваться промокодом вы можете до"
                                      data-continue-text=" 00.00.2018."
                                      placeholder="Укажите условие" readonly
                            >Воспользоваться промокодом вы можете до 00.00.2018.</textarea>
                            <div class="close-input-custom" ></div>
                        </div>
                    </div>
                    <div class="option-select-field">
                        <div class="block-textarea-custom hidden">
                            <textarea data-preview-text="Промокод действует на одного человека"
                                      data-continue-text=". Если идете компанией, необходимо приобретать промокоды на каждого."
                                      placeholder="Укажите условие" readonly
                            >Промокод действует на одного человека. Если идете компанией, необходимо приобретать промокоды на каждого.</textarea>
                            <div class="close-input-custom" ></div>
                        </div>
                    </div>
                    <div class="option-select-field">
                        <div class="block-textarea-custom hidden">
                            <textarea data-preview-text="Необходимо предъявлять промокод до заказа"
                                      data-continue-text=". Скидка предоставляется только при наличии неиспользованного ранее промокода, вы можете его назвать по телефону, предъявить в распечатанном или в электронном виде."
                                      placeholder="Укажите условие" readonly
                            >Необходимо предъявлять промокод до заказа. Скидка предоставляется только при наличии неиспользованного ранее промокода, вы можете его назвать по телефону, предъявить в распечатанном или в электронном виде.</textarea>
                            <div class="close-input-custom" ></div>
                        </div>
                    </div>
                    <div class="option-select-field">
                        <div class="block-textarea-custom hidden">
                            <textarea data-preview-text="Обязателен предварительный заказ"
                                      data-continue-text=" или бронь по телефонам, указанным в купоне на скидку."
                                      placeholder="Укажите условие" readonly
                            >Обязателен предварительный заказ или бронь по телефонам, указанным в купоне на скидку.</textarea>
                            <div class="close-input-custom" ></div>
                        </div>
                    </div>
                    <div class="option-select-field">
                        <div class="block-textarea-custom hidden">
                            <textarea data-preview-text="Скидка по промокоду не суммируется"
                                      data-continue-text=" с другими акциями и спецпредложениями."
                                      placeholder="Укажите условие" readonly
                            >Скидка по промокоду не суммируется с другими акциями и спецпредложениями.</textarea>
                            <div class="close-input-custom" ></div>
                        </div>
                    </div>
                    <div class="option-select-field">
                        <div class="block-textarea-custom hidden">
                            <textarea data-preview-text="Поставщик несет полную ответственность"
                                      data-continue-text=" перед потребителем за достоверность информации."
                                      placeholder="Укажите условие" readonly
                            >Поставщик несет полную ответственность перед потребителем за достоверность информации.</textarea>
                            <div class="close-input-custom" ></div>
                        </div>
                    </div>
                    <div class="option-select-field">
                        <div class="block-textarea-custom hidden">
                            <textarea data-preview-text='Услуги (товары) предоставляются ООО "Рестгорсервис" УНП 191206305.'
                                      data-continue-text=""
                                      placeholder="Укажите условие" readonly
                            >Услуги (товары) предоставляются ООО "Рестгорсервис" УНП 191206305.</textarea>
                            <div class="close-input-custom" ></div>
                        </div>
                    </div>
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
        </div>

        <div class="container-add-place">
            <div class="block-field-setting">
                <label class="label-field-setting">Стоимость товара или услуги (руб)</label>
                <input id="price" name="discount[price]" class="input-field-setting"
                       placeholder="Укажите цену, если это возможно" value="<?=$discount->price ?? ''?>">
            </div>

            <div class="block-field-setting">
                <label class="label-field-setting">Скидка (%)</label>
                <input id="discount" name="discount[discount]" class="input-field-setting"
                       placeholder="Укажите скидку" value="<?=$discount->discount?>">
            </div>

            <div class="block-field-setting">
                <label class="label-field-setting">Экономия</label>
                <input id="economy" class="input-field-setting"
                       placeholder="&#8734;" value="" readonly>
            </div>
            <div class="block-field-setting">
                <label class="label-field-setting">Цена со скидкой</label>
                <input id="price-with-discount" class="input-field-setting"
                       placeholder="&#8734;" value="" readonly>
            </div>
            <div class="block-field-setting">
                <label class="label-field-setting">Колличество промокодов</label>
                <input id="product-count" name="discount[number_purchases]" class="input-field-setting"
                       placeholder="Укажите колличество" value="<?=$discount->number_purchases?>">
            </div>
        </div>

        <div class="container-add-place">
            <div class="container-description">
                <div class="description-header">Описание акции</div>
                <div class="block-write-editors">
                    <input id="article" name="discount[data]" type="text" style="display: none"
                           data-upload-by-url="/discount/upload-new-photo-by-url?postId=<?=$discount->post_id?>"
                           data-upload-by-file="/discount/upload-new-photo?postId=<?=$discount->post_id?>">

                    <?php if($discount->data):?>
                        <?=Helper::parserForEditor($discount->data, true);?>
                    <?php else:?>
                        <div class="item item-editor-default container-editor">
                            <div class="editable"></div>
                        </div>
                    <?php endif;?>
                </div>
            </div>
        </div>

        <div class="container-add-place">
            <div class="container-gallery">
                <div class="gallery-header">Галерея</div>

                <?=$this->render('__edit_photos', [
                    'discount' => $discount,
                    'photos' => $photos,
                ])?>

                <label class="btn-add-photo-preview" for="discount-gallery">Добавить фотографии</label>
            </div>
            <input style="display: none" id="discount-gallery" name="discount-gallery" type="file" multiple
                   accept="image/*,image/jpeg,image/gif,image/png">
        </div>

        <div class="container-add-place">
            <div class="block-field-setting">
                <label class="label-field-setting">Заголовок для поисковиков</label>
                <input name="discount[title]" class="input-field-setting" placeholder="Введите текст"
                       value="<?=$discount->title?>">
            </div>
            <div class="block-field-setting">
                <label class="label-field-setting">Описание для поисковиков</label>
                <input name="discount[description]" class="input-field-setting" placeholder="Введите текст"
                       value="<?=$discount->description?>">
            </div>
            <div class="block-field-setting">
                <label class="label-field-setting">Ключевые слова</label>
                <input name="discount[key_word]" class="input-field-setting" placeholder="Введите текст"
                       value="<?=$discount->key_word?>">
            </div>
            <div class="btn-send">
                <div id="edit-discount" class="large-wide-button"><p>Редактировать</p></div>
            </div>
        </div>


    </form>
</div>
<div style="margin-bottom:30px;"></div>

<script>
    $(document).ready(function () {
        <?php if (isset($errors[0])):?>
            $().toastmessage('showToast', {
                text: '<?=$errors[0]?>',
                stayTime: 5000,
                type: 'error'
            });
        <?php endif;?>

        editable.init('.editable', {
            toolbar: {
                photo: true,
                video: true,
                text: true
            }
        });

        $("#datepicker").datepicker({
            defaultDate: new Date(<?=$discount->date_finish * 1000?>),
            minDate: 0,
            onSelect: function(dateText, inst) {
                var date = $(this).datepicker("getDate");

                $('#end-share-date').text(dateText);
                $('#date_finish').val(Math.round(date.getTime()/1000));
            }
        });

        $('#price').mask("###0.00", {reverse: true});
        $('#discount').mask("#0", {reverse: true});
        $('#product-count').mask("#0", {reverse: true});

        $('.block-inputs').sortable();
        $('textarea').autosize();
        $('#price').trigger('input');

        $(document).off('click','#edit-discount')
            .on('click','#edit-discount', function () {
                if (editable.parserEditable()) {
                    $('#edit-discount-form').submit();
                }
            });
    })
</script>