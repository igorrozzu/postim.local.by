<div class="margin-top60"></div>
<div class="block-content">
    <form action="/post/save-post" id="post-form" method="post">
        <div class="container-add-place">
            <div class="block-field-setting">
                <label class="label-field-setting">Название места</label>
                <input name="name" class="input-field-setting validator" data-error-parents="block-field-setting" data-message="Ввидите название" data-regex="^\S.{3,}" placeholder="Введите название" value="">
            </div>
            <div class="block-field-setting">
                <label class="label-field-setting">Категория</label>
                <div class="selectorFields" id="categories" data-is-many="true" data-id="categories" data-max="3"
                     data-info='<?= \yii\helpers\Json::encode($params['categories']) ?>'>
                    <div class="block-inputs"></div>
                    <div class="between-selected-field btn-open-field" data-open=false>
                        <input class="search-selected-field" type="button" data-value="Выберите категорию"
                               value="Выберите категорию" placeholder="Выберите категорию">
                        <div class="open-select-field2"></div>
                    </div>
                    <div class="container-scroll-fields">
                        <div class="container-options"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-add-place">
            <div class="block-field-setting" style="border-bottom: 0px">
                <label class="label-field-setting">Адрес</label>
                <div class="selectorFields" data-is-many="false" data-id="city" data-max="1"
                     data-info='<?= \yii\helpers\Json::encode($params['cities']) ?>'>
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
            <div class="block-field-setting" style="border-bottom: 0px">
                <input style="margin-top: 0px" name="address_text"  class="input-field-setting validator" placeholder="Введите адрес" data-error-parents="block-field-setting" data-message="Введите адрес" data-regex="^\S.{3,}" value="">
                <label class="label-field-setting">Комментарий к адресу</label>
                <input name="comments_to_address" class="input-field-setting" placeholder="Расстояние до метро, этаж и т.д."
                       value="">
                <input id="coords_address" style="display: none" name="coords_address" type="text">
            </div>
            <div id="map_block" class="block-map">
                <div class="btns-map">
                    <div class="find-me" title="Найти меня"></div>
                    <div class="zoom-plus"></div>
                    <div class="zoom-minus"></div>
                </div>
                <div id="map" style="display: block"></div>
            </div>
        </div>

        <div class="container-add-place">
            <div class="block-field-setting">
                <label class="label-field-setting">Добавить контакт</label>
                <div class="selected-field">
                    <div id="select-contact-value" class="select-value"><span
                                class="placeholder-select">Выберите контакт</span></div>
                    <div data-open-id="select-contact" class="open-select-field"></div>
                </div>
                <div id="select-contact" class="container-scroll auto-height">
                    <div class="container-option-select add-field-contact">
                        <div data-value="Телефон" class="option-select-field">Телефон</div>
                        <div data-value="Веб-сайт" class="option-select-field">Веб-сайт</div>
                        <div data-value="Вконтакте" class="option-select-field">Вконтакте</div>
                        <div data-value="Одноклассники" class="option-select-field">Одноклассники</div>
                        <div data-value="Twitter" class="option-select-field">Twitter</div>
                        <div data-value="Facebook" class="option-select-field">Facebook</div>
                        <div data-value="Instagram" class="option-select-field">Instagram</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-add-place">
            <div class="block-field-setting">
                <label class="label-field-setting">Режим работы</label>
                <div class="selected-field btns-field">
                    <div class="container-btns-field">
                        <div class="btn-all-along btn-time_work select-all">Круглосуточно</div>
                        <div class="btn-all-along btn-time_work unselect-all">Закрыто</div>
                        <div style="display: none" class="input-time-work-btn">
                            <input name="time_work[btns]" value="">
                        </div>
                    </div>
                    <div data-open-id="select-worktime" class="close-select-field"></div>
                </div>
                <div id="select-worktime" style="margin-top: 0px;" class="open-select">
                    <div class="container-row-time-work">
                        <div class="day-name">Понедельник</div>
                        <div class="day-name-min">Пн.</div>
                        <div class="container-time">
                            <span>с</span>
                            <div class="time-period"><input name="time_work[1][start]" placeholder="00:00"></div>
                            <span>до</span>
                            <div class="time-period"><input name="time_work[1][finish]" placeholder="00:00"></div>
                        </div>
                    </div>
                    <div class="container-row-time-work">
                        <div class="day-name">Вторник</div>
                        <div class="day-name-min">Вт.</div>
                        <div class="container-time">
                            <span>с</span>
                            <div class="time-period"><input name="time_work[2][start]" placeholder="00:00"></div>
                            <span>до</span>
                            <div class="time-period"><input name="time_work[2][finish]" placeholder="00:00"></div>
                        </div>
                    </div>
                    <div class="container-row-time-work">
                        <div class="day-name">Среда</div>
                        <div class="day-name-min">Ср.</div>
                        <div class="container-time">
                            <span>с</span>
                            <div class="time-period"><input name="time_work[3][start]" placeholder="00:00"></div>
                            <span>до</span>
                            <div class="time-period"><input name="time_work[3][finish]" placeholder="00:00"></div>
                        </div>
                    </div>
                    <div class="container-row-time-work">
                        <div class="day-name">Четверг</div>
                        <div class="day-name-min">Чт.</div>
                        <div class="container-time">
                            <span>с</span>
                            <div class="time-period"><input name="time_work[4][start]" placeholder="00:00"></div>
                            <span>до</span>
                            <div class="time-period"><input name="time_work[4][finish]" placeholder="00:00"></div>
                        </div>
                    </div>
                    <div class="container-row-time-work">
                        <div class="day-name">Пятница</div>
                        <div class="day-name-min">Пт.</div>
                        <div class="container-time">
                            <span>с</span>
                            <div class="time-period"><input name="time_work[5][start]"  placeholder="00:00"></div>
                            <span>до</span>
                            <div class="time-period"><input name="time_work[5][finish]"  placeholder="00:00"></div>
                        </div>
                    </div>
                    <div class="container-row-time-work">
                        <div class="day-name">Суббота</div>
                        <div class="day-name-min">Сб.</div>
                        <div class="container-time">
                            <span>с</span>
                            <div class="time-period"><input name="time_work[6][start]"  placeholder="00:00"></div>
                            <span>до</span>
                            <div class="time-period"><input name="time_work[6][finish]"  placeholder="00:00"></div>
                        </div>
                    </div>
                    <div class="container-row-time-work">
                        <div class="day-name">Воскресенье</div>
                        <div class="day-name-min">Вск.</div>
                        <div class="container-time">
                            <span>с</span>
                            <div class="time-period"><input name="time_work[7][start]"  placeholder="00:00"></div>
                            <span>до</span>
                            <div class="time-period"><input name="time_work[7][finish]"  placeholder="00:00"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--TODO дополнения -->

        <div class="container-add-place block-features">

        </div>

        <!--TODO описание места-->



        <div class="container-add-place">
            <div class="container-description">
                <div class="description-header">Описание места</div>
                <div class="block-write-editors">
                    <input id="article" name="article" type="text" style="display: none">
                    <div class="item item-editor-default container-editor">
                        <div class="editable"></div>
                    </div>
                </div>
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

		<?php if(Yii::$app->user->identity->role > 1):?>
            <div class="container-add-place">
                <div class="container-gallery">
                    <div class="gallery-header">Галерея</div>
                    <div class="block-inputs-gallery" style="display: none">

                    </div>
                    <div class="block-gallery">

                    </div>
                    <div class="btn-add-photos-gallery">Добавить фотографии</div>
                </div>
                <input style="display: none" class="photo-add" name="photo-add" type="file" multiple accept="image/*,image/jpeg,image/gif,image/png">
            </div>

            <div class="container-add-place">
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
                <div class="btn-setting-save">
                    <div class="large-wide-button"><p>Опубликовать</p></div>
                </div>
            </div>

		<?php endif;?>
    </form>
</div>
<div style="margin-bottom:30px;"></div>
<script>
    $(document).ready(function () {
        $('.block-inputs').sortable();

		var maskBehavior = function (val) {
			val = val.split(":");
			return (parseInt(val[0]) > 19)? "HZ:M0" : "H0:M0";
		};

		spOptions = {
			onKeyPress: function(val, e, field, options) {
				field.mask(maskBehavior.apply({}, arguments), options);
			},
			translation: {
				'H': { pattern: /[0-2]/, optional: false },
				'Z': { pattern: /[0-3]/, optional: false },
				'M': { pattern: /[0-5]/, optional: false}
			}
		};

        $('.container-time .time-period input').mask(maskBehavior, spOptions);
		map.init({lat:53.905219, lon:27.564271},11);
		map.initEventClickMap(post_add.setPlace);
	})
</script>