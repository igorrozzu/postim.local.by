<div class="margin-top60"></div>
<div class="block-content">
    <div class="container-add-place">
        <div class="block-field-setting">
            <label class="label-field-setting">Название места</label>
            <input name="name" class="input-field-setting" placeholder="Пример: Парк Горькова" value="">
        </div>
        <div class="block-field-setting">
            <label class="label-field-setting">Категория</label>
            <div class="selectorFields" data-is-many="true" data-id="categories" data-max="3"
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
            <input style="margin-top: 0px" name="address_text" class="input-field-setting" placeholder="Введите адрес"
                   value="">
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
                <div data-open-id="select-worktime" class="open-select-field"></div>
            </div>
            <div id="select-worktime" style="margin-top: 0px;" class="">
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

    <div class="container-add-place">
        <div class="block-field-setting">
            <label class="label-field-setting">Средний чек (руб)</label>
            <input class="input-field-setting" placeholder="Ввидите сумму" value="">
        </div>
        <div class="block-field-setting">
            <label class="label-field-setting">Кухня</label>
            <div class="selected-field">
                <div id="select-kitchen-value" data-value="" class="select-value"><span class="placeholder-select">Выберите кухню</span></div>
                <div data-open-id="select-kitchen" class="open-select-field"></div>
            </div>
            <div id="select-kitchen" class="container-scroll auto-height">
                <div class="container-option-select option-active">
                    <div data-value="Итальянская" class="option-select-field">Итальянская</div>
                    <div data-value="Китайская" class="option-select-field">Китайская</div>
                    <div data-value="Японская" class="option-select-field">Японская</div>
                    <div data-value="Немецкая" class="option-select-field">Немецкая</div>
                    <div data-value="Французская" class="option-select-field">Французская</div>
                    <div data-value="Русская" class="option-select-field">Русская</div>
                    <div data-value="Белорусская" class="option-select-field">Белорусская</div>
                    <div data-value="Украинская" class="option-select-field">Украинская</div>
                    <div data-value="Кавказская" class="option-select-field">Кавказская</div>
                    <div data-value="Турецкая" class="option-select-field">Турецкая</div>
                </div>
            </div>
        </div>
        <div class="block-field-setting">
            <label class="label-field-setting">Особенности</label>
            <div class="selected-field btns-field">
                <div class="container-btns-field">
                    <div class="btn-all-along btn-time_work">Еда с собой</div>
                    <div class="btn-all-along btn-time_work">Доставка еды</div>
                    <div class="btn-all-along btn-time_work">Wi-Fi</div>
                </div>
                <div class="open-select-field"></div>
            </div>
        </div>
    </div>
    <div class="container-add-place">
        <div class="container-description">
            <div class="description-header">Описание места</div>
            <div class="block-write-description">
                <textarea placeholder="Введите текст"></textarea>
            </div>
            <div class="btns-write-description">
                <div class="btn-write-description btn-source-min"></div>
                <div class="btn-write-description btn-h-min"></div>
                <div class="btn-write-description btn-play-min"></div>
            </div>
        </div>
    </div>
    <div class="container-add-place">
        <div class="container-gallery">
            <div class="gallery-header">Галерея</div>
            <div class="block-gallery">
                <div class="item-photo-from-gallery" style="background-image: url('testP.png')">
                    <div class="container-blackout">
                        <div class="header-btns">
                            <span class="btn-item-photo btn-close-photo-gallery"></span>
                        </div>
                        <div class="footer-btns">
                            <span class="btn-item-photo btn-confirm-photo-gallery"></span>
                            <span class="btn-item-photo btn-edit-photo-gallery"></span>
                        </div>
                    </div>
                </div>
                <div class="item-photo-from-gallery" style="background-image: url('testP.png')">
                    <div class="container-blackout">
                        <div class="header-btns">
                            <span class="btn-item-photo btn-close-photo-gallery"></span>
                        </div>
                        <div class="footer-btns">
                            <span class="btn-item-photo btn-confirm-photo-gallery"></span>
                            <span class="btn-item-photo btn-edit-photo-gallery"></span>
                        </div>
                    </div>
                </div>
                <div class="item-photo-from-gallery" style="background-image: url('testP.png')">
                    <div class="container-blackout">
                        <div class="header-btns">
                            <span class="btn-item-photo btn-close-photo-gallery"></span>
                        </div>
                        <div class="footer-btns">
                            <span class="btn-item-photo btn-confirm-photo-gallery"></span>
                            <span class="btn-item-photo btn-edit-photo-gallery"></span>
                        </div>
                    </div>
                </div>
                <div class="item-photo-from-gallery" style="background-image: url('testP.png')">
                    <div class="container-blackout">
                        <div class="header-btns">
                            <span class="btn-item-photo btn-close-photo-gallery"></span>
                        </div>
                        <div class="footer-btns">
                            <span class="btn-item-photo btn-confirm-photo-gallery"></span>
                            <span class="btn-item-photo btn-edit-photo-gallery"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="btn-add-photos-gallery">Добавить фотографии</div>
        </div>
    </div>
    <div class="container-add-place">
        <div class="block-field-setting">
            <label class="label-field-setting">Заголовок для поисковиков</label>
            <input class="input-field-setting" placeholder="Введите текст" value="">
        </div>
        <div class="block-field-setting">
            <label class="label-field-setting">Описание для поисковиков</label>
            <input class="input-field-setting" placeholder="Введите текст" value="">
        </div>
        <div class="block-field-setting">
            <label class="label-field-setting">Ключевые слова</label>
            <input class="input-field-setting" placeholder="Введите текст" value="">
        </div>
        <div class="btn-setting-save">
            <div class="large-wide-button"><p>Опубликовать</p></div>
        </div>
    </div>
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