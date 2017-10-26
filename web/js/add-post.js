var Post_add = (function (window, document, undefined, $) {

    return function () {

        var that = {

            initEvents: function () {
                $(document).off('click', '.btn-open-field')
                    .on('click', '.btn-open-field', function (e) {
                        if ($(this).data('open') == false) {
							that.selectorFields.closeSelector();
                            that.selectorFields.init({
                                $selectedFieldContainer: $(this).parents('.selectorFields')
                            });
                            $(this).data('open',true);
                            $(this).attr('data-open',true);
                        } else {
							if($(e.target).is('input')) return false;
							that.selectorFields.destruction({
								$selectedFieldContainer: $(this).parents('.selectorFields')
							});
							$(this).data('open',false);
							$(this).attr('data-open',false);
                        }
                    });

				$(document).off('click','.close-selected-option')
					.on('click','.close-selected-option',function () {
						that.selectorFields.showSelected($(this).parents('.selectorFields')
							.find('.between-selected-field')
							.selector
						);
						$(this).parents('.btn-selected-option').remove();
						that.selectorFields.closeSelector();
					});

				$(document).off('click', '.add-field-contact .option-select-field')
					.on('click', '.add-field-contact .option-select-field', function () {
						var caseField = $(this).data('value');
						var elemAfterInsert = $('#' + $(this).parents('.container-scroll').attr('id') + '-value').parents('.selected-field');
						that.contacts.init(elemAfterInsert);
						that.contacts.caseStart(caseField);
						if(caseField == 'Веб-сайт'){
							$(this).hide();
						}
						$('#' + $(this).parents('.container-scroll').attr('id') + '-value').click();
					});

				$(document).off('keydown','.validator')
					.on('keydown','.validator',function () {
						var $input = $(this);
						setTimeout(function () {
							var regexp = new RegExp($input.attr('data-regex'));
							if(!regexp.test($input.val())){
								$input.parents('.'+$input.attr('data-error-parents')).addClass('error');
								that.validation.addError({$elem:$input,message:$input.attr('data-message')});
							}else {
								if($input.parents('.'+$input.attr('data-error-parents')).hasClass('error')){
									$input.parents('.'+$input.attr('data-error-parents')).removeClass('error');
									that.validation.removeError($input);
								}
							}
						},10)
					});

				$(document).off('click', '.close-input-contact')
					.on('click', '.close-input-contact', function () {
						$(this).parents('.block-input-contact').remove();
						if($(this).parents('.block-input-contact').find('input[name="contacts[web_site]"]').length){
							$('.add-field-contact .option-select-field[data-value="Веб-сайт"]').show();
						}
					});
				$(document).off('click','.select-all')
					.on('click', '.select-all', function () {
						if($(this).hasClass('active')){
							that.workTime.clearBtns(true);
						}else {
							that.workTime.clearBtns(true);
							that.workTime.selectedAll();
							$(this).addClass('active');
						}


					});
				$(document).off('click','.unselect-all')
					.on('click', '.unselect-all', function () {
						if($(this).hasClass('active')){
							that.workTime.clearBtns(true);
						}else {
							that.workTime.clearBtns(true);
							$(this).addClass('active');
							that.workTime.unSelectedAll()
						}


					});

				$(document).off('click','#categories .option-select-field')
					.on('click','#categories .option-select-field',function () {
						that.features.getFeatures()
					});

				$(document).off('click','#categories .close-selected-option')
					.on('click','#categories .close-selected-option',function () {
						that.features.getFeatures()
					});
				$(document).off('change','.photo-add').on('change','.photo-add',function (e) {
					that.photos.addPhotos.call(this, e);
				});
				$(document).off('click','.btn-add-photos-gallery')
					.on('click','.btn-add-photos-gallery',function () {
						$('.photo-add').trigger('click');
					});
				$(document).off('click','.btn-close-photo-gallery')
					.on('click','.btn-close-photo-gallery',function () {
						that.photos.deletePhoto($(this).parents('.item-photo-from-gallery').attr('id'));
					});
				$(document).off('click','.btn-edit-photo-gallery')
					.on('click','.btn-edit-photo-gallery',function () {
						that.photos.editPhoto($(this).parents('.item-photo-from-gallery').attr('id'));
					});
				$(document).off('click','.close-photo-info')
					.on('click','.close-photo-info',function () {
						$(this).parents('.container-blackout-popup-window').hide();
					});
				$(document).off('click','.btn-confirm-photo-gallery')
					.on('click','.btn-confirm-photo-gallery',function () {
						that.photos.confirmPhoto($(this).parents('.item-photo-from-gallery').attr('id'));
						$('.btn-confirm-photo-gallery').removeClass('active');
						$(this).addClass('active');
					});
				$(document).off('click','.btn-place-save')
					.on('click','.btn-place-save',function () {
						if(editable.parserEditable()){
							if(that.validation.validate()){
								$('#post-form').submit();
							}else {
								that.showMessage(that.validation.getLastMessage(),'error');
							}

						}
					});

				that.workTime.initInputEvents();

            },
			setPlace: function (latlng) {
				var $block_coords_address = $('#coords_address');
				$block_coords_address.val(latlng.lat + ',' + latlng.lng);
				$block_coords_address.attr('value',latlng.lat + ',' + latlng.lng);
			},
            selectorFields: {
                $selectedFieldContainer: null,
				$containerOptions: null,
                is_many: false,
				max_block:null,
                $inputSearch:null,
                info: null,
				id: null,

                init: function (p) {

                    that.selectorFields.$selectedFieldContainer = p.$selectedFieldContainer;
                    that.selectorFields.$inputSearch = that.selectorFields.$selectedFieldContainer.find('.search-selected-field');
                    that.selectorFields.$containerOptions = that.selectorFields.$selectedFieldContainer.find('.container-options');

                    if (that.selectorFields.$selectedFieldContainer.data('is-many')) {
                        that.selectorFields.is_many = true;
						that.selectorFields.max_block = that.selectorFields.$selectedFieldContainer.data('max');
                    }else{
                    	that.selectorFields.max_block = 1;
						that.selectorFields.is_many = false;
					}

					that.selectorFields.id = that.selectorFields.$selectedFieldContainer.data('id');

                    that.selectorFields.$selectedFieldContainer.addClass('active');
                    that.selectorFields.info = that.selectorFields.$selectedFieldContainer.data('info');
                    that.selectorFields.updateInfo();

					that.selectorFields.$inputSearch.attr('type','text');
					that.selectorFields.$inputSearch.val('');

					that.selectorFields.$inputSearch.blur();
					that.selectorFields.$inputSearch.focus();

					that.selectorFields.renderLists(that.selectorFields.info);

                    that.selectorFields.initScrollBar(that.selectorFields.$selectedFieldContainer.find('.container-scroll-fields'));
                    that.selectorFields.initEvents();

                },

				renderLists:function (lists) {
					var $container_tmp = $('<div></div>');
					var $list_tmp = $('<div data-value="" class="option-select-field"></div>');
					if (that.selectorFields.$containerOptions != null) {
						var number = lists.length;
						for (var i = 0; i < number; i++){
							$list_tmp.attr('data-value',lists[i].id);
							$list_tmp.text(lists[i].name);
							$container_tmp.append($list_tmp.clone());
						}

						that.selectorFields.$containerOptions.html($container_tmp.html());
					}
				},

                initEvents: function () {
                    $(document).on('keydown',that.selectorFields.$inputSearch.selector,function () {
                        setTimeout(function () {
							var new_lists = [];
							var textFil=that.selectorFields.$inputSearch.val();
							var expr = new RegExp('^'+textFil,'i');
							var number = that.selectorFields.info.length;

							for (var i = 0; i < number; i++){

								if (expr.test(that.selectorFields.info[i].name)) {
									new_lists.push(that.selectorFields.info[i]);
								}

							}
							that.selectorFields.renderLists(new_lists);

						},10)
					});

					if(that.selectorFields.is_many){
                    	$(document).on('click','.option-select-field',function () {

							$block_inputs = that.selectorFields.$selectedFieldContainer.find('.block-inputs');
                    		if($block_inputs.find('.btn-selected-option').length < that.selectorFields.max_block){
								var $content = $('<div class="btn-selected-option"><span class="option-text">Текст</span> <span class="close-selected-option"></span> <input name="" value="" style="display: none"> </div>');

								$content.find('.option-text').text($(this).text());
								$content.find('input').val($(this).attr('data-value'));
								$content.find('input').attr('value', $(this).attr('data-value'));
								$content.find('input').attr('name',that.selectorFields.id+'[]');
								$block_inputs.append($content.clone());

								if(($block_inputs.find('.btn-selected-option').length == that.selectorFields.max_block)){
									that.selectorFields.hideSelected(that.selectorFields.$selectedFieldContainer
										.find('.between-selected-field')
									);
								}

								that.selectorFields.closeSelector();
							}else {
                    			// TODO показать сообщение о ошибки
							}
						})
					}else {

						$(document).on('click','.option-select-field',function () {

							$block_inputs = that.selectorFields.$selectedFieldContainer.find('.block-inputs');
							$block_inputs.css('display','none');

							var $content = $('<div class="btn-selected-option"><span class="option-text">Текст</span> <span class="close-selected-option"></span> <input name="" value="" style="display: none"> </div>');

							$content.find('.option-text').text($(this).text());
							$content.find('input').val($(this).attr('data-value'));
							$content.find('input').attr('value', $(this).attr('data-value'));
							$content.find('input').attr('name',that.selectorFields.id);
							$block_inputs.html($content.clone());
							that.selectorFields.$inputSearch.attr('data-value',$(this).text()).css('color','#444444');

							if(that.selectorFields.id == 'city'){
								that.selectorFields.moveToMapByCity(that.selectorFields.$inputSearch.attr('data-value'));

								if($(this).text() == 'Минск'){
									that.selectorFields.openBlockMetro();
								}else {
                                    that.selectorFields.closeBlockMetro();
								}
							}

							that.selectorFields.closeSelector();

						})
					}

                },

                destruction: function () {
                	that.selectorFields.destructionEvents();
					that.selectorFields.$selectedFieldContainer.removeClass('active');
					that.selectorFields.destructScrollBar(that.selectorFields.$selectedFieldContainer.find('.container-scroll-fields'));
                    that.selectorFields.$selectedFieldContainer = null;
					that.selectorFields.$containerOptions = null;

					that.selectorFields.$inputSearch.attr('type','button');
					that.selectorFields.$inputSearch.val(that.selectorFields.$inputSearch.attr('data-value')).blur();

					that.selectorFields.is_many = false;
                    that.selectorFields.$inputSearch = null;
                    that.selectorFields.info = null;
                    that.selectorFields.max_block = null;
					that.selectorFields.id = null;
                },
                destructionEvents: function () {
					$(document).off('keydown',that.selectorFields.$inputSearch.selector);
					$(document).off('click','.option-select-field');

                },
				initScrollBar: function ($container) {
                	main.initCustomScrollBar($container,{scrollInertia: 50});
				},
				destructScrollBar: function ($container) {
					$container.mCustomScrollbar('destroy');
				},
				closeSelector: function () {
					if (that.selectorFields.$selectedFieldContainer != null) {
						that.selectorFields.$selectedFieldContainer
							.find('.between-selected-field')
							.data('open', false)
							.attr('data-open', false);
						that.selectorFields.destruction({
							$selectedFieldContainer: that.selectorFields.$selectedFieldContainer
						})
					}
				},
				updateInfo: function () {
                	var new_info = [];
                	var someIds = {};

                	var $block_inputs = that.selectorFields.$selectedFieldContainer.find('.block-inputs');
					that.selectorFields.info = that.selectorFields.$selectedFieldContainer.data('info');
                	$block_inputs.find('.btn-selected-option').each(function () {
						someIds[$(this).find('input').val()] = false;
					});

					var number = that.selectorFields.info.length;

					for(var i = 0; i< number; i++){
						if(that.selectorFields.info[i].id in someIds){

						}else {
							new_info.push(that.selectorFields.info[i]);
						}
					}

					that.selectorFields.info = new_info;

				},
				hideSelected: function ($elem) {
					$elem.hide();
					$elem.parents('.selectorFields').find('.block-inputs').css({marginBottom:'20px'})
				},
				showSelected: function (selector) {
					$(selector).show();
					$(selector).parents('.selectorFields').find('.block-inputs').css({marginBottom:'0'})
				},

				moveToMapByCity:function(name){
					$.ajax({
						url: '/site/get-coords-by-address',
						type: "get",
						dataType: "json",
						data: {
							address: name
						},
						success: function (response) {
							if(response.error == false){
								map.moveToMap({lat:response.location.lat,lon:response.location.lng},response.zoom)
							}
						}
					});
				},

				openBlockMetro: function () {
					$('.block-field-setting.metro').show();
                },

                closeBlockMetro: function () {
                    $('.block-field-setting.metro').hide();
                    $('.block-field-setting.metro').find('.block-inputs').html();

                },


            },
            contacts: {
				$blockInputContact : null,
				$inputContact : null,
				$btnClose : null,
				$elemAfterInsert : null,

				init: function ($elemAfterInsert) {
					that.contacts.$elemAfterInsert = $elemAfterInsert;
					that.contacts.$btnClose = $('<div class="close-input-contact"></div>');
					that.contacts.$blockInputContact = $('<div class="block-input-contact"><span class="container-img"><img src="img/icon-phone-min.png"></span></div>');
					that.contacts.$inputContact = $('<input class="validator" data-error-parents="block-input-contact" data-message="Некоректные данные для контактов" placeholder="Введите номер телефона">');
				},

				batchInsert: function () {
					that.contacts.$blockInputContact.append(that.contacts.$inputContact);
					that.contacts.$blockInputContact.append(that.contacts.$btnClose);
					that.contacts.$elemAfterInsert.before(that.contacts.$blockInputContact);
				},

				caseStart: function (caseField) {
					function createBlock(img,placeholder,name,regix) {
						that.contacts.$blockInputContact.find('img').attr('src',img);
						that.contacts.$inputContact.attr('placeholder',placeholder);
						that.contacts.$inputContact.attr('name',name);
						that.contacts.$inputContact.attr('data-regex',regix);
						that.contacts.batchInsert();
					}
					switch (caseField){
						case 'Телефон':{
							createBlock('/img/icon-phone-min.png','Номер телефона','contacts[phones][]','^[0-9 +-]{3,20}$')
						}break;
						case 'Веб-сайт':{
							createBlock('/img/icon-link-min.png',
								'Ссылка на сайт', 'contacts[web_site]',
								'^(https?:\\/\\/)?([\\da-z\.-]+)\\.([a-z\\.]{2,6})([\\/\\w \\.-]*)*\\/?$'
							);
						}break;
						case 'Вконтакте':{
							createBlock('/img/icon-vk-min.png','https://vk.com/...','contacts[social_networks][][vk]','^https:\\/\\/vk.com\\/.+$','')
						}break;
						case 'Одноклассники':{
							createBlock('/img/icon-ok-min.png',
								'https://www.ok.ru/...', 'contacts[social_networks][][ok]',
								'^https:\\/\\/(www\\.)?ok\\.ru\\/.+$'
							);
						}break;
						case 'Twitter':{
							createBlock('/img/icon-tw-min.png',
								'https://twitter.com/...', 'contacts[social_networks][][tw]',
								'^https:\\/\\/twitter.com\\/.+$'
							);
						}break;
						case 'Facebook':{
							createBlock('/img/icon-fb-min.png',
								'https://www.facebook.com/...', 'contacts[social_networks][][fb]',
								'^https:\\/\\/(www\\.)?facebook\\.com\\/.+$'
							);
						}break;
						case 'Instagram':{
							createBlock('/img/icon-instagram-min.png',
								'https://www.instagram.com/...', 'contacts[social_networks][][inst]',
								'^https?:\\/\\/(www\\.)?instagram\\.com\\/.+$'
							);
						}break;

					}

				}
			},
			workTime: {
            	is_btn_active: false,
				selectedAll: function () {
					var $btn_input = $('.input-time-work-btn input');
					$btn_input.attr('value','select');
					$btn_input.val('select');
					$('.close-select-field').trigger('click');
					that.workTime.is_btn_active = true;
					that.workTime.clearAllError();
				},
				unSelectedAll: function () {
					var $btn_input = $('.input-time-work-btn input');
					$btn_input.attr('value','unselect');
					$btn_input.val('unselect');
					$('.close-select-field').trigger('click');
					that.workTime.is_btn_active = true;
					that.workTime.clearAllError();
				},
				clearBtns: function (all) {
					if(all){
						var $timeInputs = $('.container-time .time-period input');
						$timeInputs.val('');
						$timeInputs.attr('value','');
					}

					$('.unselect-all').removeClass('active');
					$('.select-all').removeClass('active');
					$('.input-time-work-btn input').attr('value','');
					$('.input-time-work-btn input').val('');
					that.workTime.is_btn_active = false;
				},
				initInputEvents:function () {
					$(document).off('keydown', '.container-time .time-period input')
						.on('keydown', '.container-time .time-period input', function () {
							var __this = this;
							setTimeout(function () {
								that.workTime.checkValid($(__this).parents('.container-row-time-work'));

								if(that.workTime.is_btn_active){
									that.workTime.clearBtns(false);
								}
							},10);

						})
				},
				clearAllError: function () {
					$('.container-row-time-work').each(function () {
						that.validation.removeError($(this));
						$(this).find('.error').removeClass('error');
					})
				},
				checkValid: function ($parent) {
					var is_valid = true;
					var $input1 =  $parent.find('.time-period input').eq(0);
					var $input2 =  $parent.find('.time-period input').eq(1);

					if($input1.val().length !== 0 || $input2.val().length !== 0){
						if($input1.val().length !==5){
							$input1.parents('.time-period').addClass('error');
							is_valid = false;
						}else {
							$input1.parents('.time-period').removeClass('error');
						}
						if($input2.val().length !==5){
							$input2.parents('.time-period').addClass('error');
							is_valid = false;
						}else {
							$input2.parents('.time-period').removeClass('error');
						}
					}else {
						$input2.parents('.time-period').removeClass('error');
						$input1.parents('.time-period').removeClass('error');
					}

					if(!is_valid){
						that.validation.addError({$elem:$parent,message:'Введите коректное время работы'});
					}else {
						that.validation.removeError($parent);
					}
				}
			},
			features:{
				$containerFeatures:null,

				getFeatures: function () {
					setTimeout(function () {
						var arrayQuery = [];

						$('#categories .block-inputs input').each(function () {
							arrayQuery.push($(this).val());
						});

						$.ajax({
							url: '/post/get-features-by-categories',
							type: "get",
							dataType: "json",
							data: {
								categories: arrayQuery
							},
							success: function (response) {
								that.features.createFeaturesContainer(response);
							}
						});
					}, 30);
				},

				createFeaturesContainer: function (features) {
					that.features.$containerFeatures = $('<div></div>');
					var number = features.rubrics.length;

					var $typeBlock2 = $('<div class="block-field-setting" id="">' +
							'<label class="label-field-setting"> Текст</label> ' +
							'<input  name="features[id]" class="input-field-setting validator" data-error-parents="block-field-setting" data-regex="^[0-9]*[.,]?[0-9]{0,}$" data-message="Некорректно введены данные" placeholder="Текст" value=""> ' +
						'</div>');

					var $typeBlock3 = $('<div class="block-field-setting" id="">' +
							'<label class="label-field-setting">Текс</label>' +
							'<div class="selectorFields"  data-is-many="true" data-id="" data-max="1000" data-info=""> ' +
								'<div class="block-inputs"></div> ' +
								'<div class="between-selected-field btn-open-field" data-open=false> ' +
									'<input class="search-selected-field" type="button" data-value="Выберите категорию" value="Выберите категорию" placeholder="Выберите категорию">' +
									'<div class="open-select-field2"></div> ' +
								'</div>' +
								'<div class="container-scroll-fields">' +
									'<div class="container-options"></div> ' +
								'</div> ' +
							'</div> ' +
						'</div>');


					for (var i = 0; i < number; i++){
						if (features.rubrics[i].type == 2) {
							if($('#'+features.rubrics[i].id).length){
								$typeBlock2 = $('#'+features.rubrics[i].id);
								$typeBlock2.find('input').attr('value',$typeBlock2.find('input').val());
							}else {
								var text  = features.rubrics[i].name.charAt(0).toUpperCase() + features.rubrics[i].name.substr(1).toLowerCase();
								$typeBlock2.attr('id',features.rubrics[i].id);
								$typeBlock2.find('.label-field-setting').text(text);
								$typeBlock2.find('input').attr('name','features['+features.rubrics[i].id+']');
								$typeBlock2.find('input').attr('placeholder','Введите '+features.rubrics[i].name);
							}
							that.features.$containerFeatures.append($typeBlock2.clone());
						} else {
							if($('#'+features.rubrics[i].id).length){
								$typeBlock3 = $('#'+features.rubrics[i].id);
							}else {
								var text  = features.rubrics[i].name.charAt(0).toUpperCase() + features.rubrics[i].name.substr(1).toLowerCase();
								$typeBlock3.attr('id',features.rubrics[i].id);
								$typeBlock3.find('.label-field-setting').text(text);
								$typeBlock3.find('.selectorFields')
									.attr('data-id','features['+features.rubrics[i].id+']')
									.attr('data-info',JSON.stringify(features.rubrics[i].underFeatures));
								$typeBlock3.find('.search-selected-field')
									.attr('data-value','Выберите')
									.attr('value','Выберите ')
									.attr('placeholder','Выберите');
							}
							that.features.$containerFeatures.append($typeBlock3.clone());
						}
					}

					number = features.additionally.length;
					if(number > 0){
						if($('#additionally').length){
							$typeBlock3 = $('#additionally');
							$typeBlock3.find('.selectorFields')
								.attr('data-info',JSON.stringify(features.additionally));

							$('#additionally .btn-selected-option input').each(function () {
								var id = $(this).attr('value');
								var bar = false;
								for(var index = 0; index < number; index++){
									if(features.additionally[index].id == id){
										bar = true;
										break;
									}
								}
								if(bar === false){
									$(this).parents('.btn-selected-option').remove();
								}
							})

						}else {
							var text  = 'Особенности';
							$typeBlock3.attr('id','additionally');
							$typeBlock3.find('.label-field-setting').text(text);
							$typeBlock3.find('.selectorFields')
								.attr('data-id','features[additionally]')
								.attr('data-info',JSON.stringify(features.additionally));
							$typeBlock3.find('.search-selected-field')
								.attr('data-value','Выберите особенности')
								.attr('value','Выберите особенности')
								.attr('placeholder','Выберите особенности');
						}
						that.features.$containerFeatures.append($typeBlock3.clone());
					}


					that.features.renderFeatures();

				},

				renderFeatures: function () {
					$('.block-features').html(that.features.$containerFeatures.html());
				}
			},
			photos:{
				addPhotos: function (e) {
					if (uploads.validatePhotos(e.target.files)) {
						var form = new FormData();
						$.each(e.target.files, function (key, value) {
							form.append('photos[]', value);
						});
						uploads.uploadFiles('/post/upload-tmp-photo', form, that.photos.renderPhotos);
						$(this).val('');

					}else {
						$().toastmessage('showToast', {
							text     : 'Изображение должно быть в формате JPG, GIF или PNG.' +
							' Макс. размер файла: 15 МБ. Не более 10 файлов',
							stayTime:  5000,
							type     : 'error'
						});
					}
				},
				renderPhotos: function (response) {

					var $tmp = $('<div id="" class="item-photo-from-gallery" style=""> <div class="container-blackout"> <div class="header-btns"> <span class="btn-item-photo btn-close-photo-gallery"></span> </div> <div class="footer-btns"> <span class="btn-item-photo btn-confirm-photo-gallery"></span> <span class="btn-item-photo btn-edit-photo-gallery"></span> </div> </div> </div>');
					var $containerNewPhoto = $('<div></div>');
					var $blockInput = $('<div id="">' +
								'<input class="src" name="photos[link][src]" type="text">' +
								'<input class="desc" name="photos[link][description]" type="text">' +
								'<input class="confirm" name="photos[link][confirm]" type="text">' +
						'</div>');

					var $containerBlockInputs = $('<div></div>');

					if(response.success){

						var number = response.data.length;

						for (var i = 0; i < number; i++) {
							$tmp.css('background-image', 'url("/post_photo/tmp/' + response.data[i].link + '")');
							$tmp.attr('id', that.photos.getHashCode(response.data[i].link));
							$containerNewPhoto.append($tmp.clone());

							$blockInput.attr('id', 'inputs_'+that.photos.getHashCode(response.data[i].link));
							$blockInput.find('.src')
								.attr('name', 'photos[' + response.data[i].link + '][src]');
							$blockInput.find('.desc')
								.attr('name', 'photos[' + response.data[i].link + '][description]');
							$blockInput.find('.confirm')
								.attr('name', 'photos[' + response.data[i].link + '][confirm]');
							$containerBlockInputs.append($blockInput.clone());
						}

						$('.block-gallery').append($containerNewPhoto.html());
						$('.block-inputs-gallery').append($containerBlockInputs.html());

					}else {
						$().toastmessage('showToast', {
							text     : response.message,
							stayTime:  5000,
							type     : 'error'
						});
					}
				},
				deletePhoto: function (id) {
					$('#'+id).remove();
					$('#inputs_'+id).remove();
				},
				getHashCode : function(s){
					return s.split("").reduce(function(a,b){a=((a<<5)-a)+b.charCodeAt(0);return a&a},0);
				},
				editPhoto:function (id) {
					var __$containerForms = $('.container-blackout-popup-window');
					var $inputSrc = $('#inputs_'+id).find('.src');
					var $inputDesc = $('#inputs_'+id).find('.desc');

					__$containerForms.html(that.photos.getFormForEditPhoto(id, {
						src: $inputSrc.val(),
						description: $inputDesc.val()
					}))
						.show();

					$('.save-info').click(function () {
						$inputSrc.attr('value',__$containerForms.find('.src').val());
						$inputSrc.val(__$containerForms.find('.src').val());
						$inputDesc.attr('value',__$containerForms.find('.description').val());
						$inputDesc.val(__$containerForms.find('.description').val());
						__$containerForms.hide();
					})

				},
				confirmPhoto:function (id) {
					$('.block-inputs-gallery .confirm').val(false);
					$('.block-inputs-gallery .confirm').attr('value',false);


					var $inputConfirm = $('#inputs_'+id).find('.confirm');
					$inputConfirm.val(true);
					$inputConfirm.attr('value',true);

				},
				getFormForEditPhoto:function (id,info) {
					var form = null;
					$.ajax({
						url: '/post/get-photo-info',
						type: "get",
						async:false,
						data: {
							src: info.src,
							description:info.description
						},
						success: function (response) {
							form = response;
						}
					});
					return form;
				}
			},
			validation: {
				errors: [],
				dopErrors: [],
				is_valid: true,
				is_change:false,
				hash_form: null,

				check_change:function (start) {
					var textInputs =  '';

					$('#post-form').find('input').each(function () {
						textInputs += $(this).val();
					});

					if(start){

						that.validation.hash_form = that.photos.getHashCode(textInputs);
						that.validation.is_change = true;

					}else {
						var new_hash_form = that.photos.getHashCode(textInputs);
						return new_hash_form !== that.validation.hash_form;
					}

				},
				addError: function (error) {
					var id = +new Date();
					var err = error;
					if(!!!err.$elem.attr('data-error_id')){
						err.$elem.attr('data-error_id', id);
						err.id = id;
						that.validation.errors.push(err);
					}
				},
				removeError: function ($elem) {
					var number = that.validation.errors.length;
					var id = $elem.attr('data-error_id');
					$elem.removeAttr('data-error_id');

					for (var i = 0; i < number; i++) {
						if (that.validation.errors[i].id == id) {
							that.validation.errors.splice(i, 1);
						}
					}
				},
				validate:function () {
					var is_valid = true;
					that.validation.checkValidate();
					if(that.validation.errors.length !== 0) {
						var number = that.validation.errors.length;
						for (var i = 0; i < number; i++) {
							if ($('[data-error_id='+that.validation.errors[i].id+']').length) {
								is_valid = false;
							}else {
								that.validation.errors.splice(i, 1);
							}
						}
						if(!is_valid){
							return is_valid;
						}
					}

					that.validation.dopErrors = [];

					if(that.validation.is_change){
						if(!that.validation.check_change(false)){
							that.validation.dopErrors.push({message:'Нашли неточность или ошибку, исправьте или дополните информацию'});
							is_valid = false;
						}
					}

					if(!$('.input-time-work-btn input').val()){
						var is_valid_w = false;

						$('.container-row-time-work input').each(function () {
							if($(this).val()){
								is_valid_w = true;
							}
						});

						if(!is_valid_w){
							is_valid = false;
							that.validation.dopErrors.push({message:'Режим работы, обязательно для заполнения'});
						}

					}

					$('.block-field-setting .block-input-contact input').each(function () {
						var value = $(this).val();
						var message = $(this).attr('data-message');
						var regexp = new RegExp($(this).attr('data-regex'));
						if(!regexp.test(value)){
							is_valid = false;
							that.validation.dopErrors.push({message: message});
						}
					});

					if (!$('#coords_address').val()) {
						is_valid = false;
						that.validation.dopErrors.push({message: 'Отметьте место на карте, перетащите и кликните по значку'});
					}

					if ($('input[name="city[]"]', '.btn-selected-option').length == 0) {
						is_valid = false;
						that.validation.dopErrors.push({message: 'Выберите город'});
					}

					if ($('input[name="categories[]"]', '.btn-selected-option').length == 0) {
						is_valid = false;
						that.validation.dopErrors.push({message: 'Выберите категорию'});
					}


					return is_valid;

				},
				checkValidate:function () {
					$('input.validator').each(function () {
						var $input = $(this);
						var regexp = new RegExp($input.attr('data-regex'));
						if(!regexp.test($input.val())){
							$input.parents('.'+$input.attr('data-error-parents')).addClass('error');
							that.validation.addError({$elem:$input,message:$input.attr('data-message')});
						}else {
							if($input.parents('.'+$input.attr('data-error-parents')).hasClass('error')){
								$input.parents('.'+$input.attr('data-error-parents')).removeClass('error');
								that.validation.removeError($input);
							}
						}
					});
				},
				getLastMessage: function () {
					var number = that.validation.errors.length;
					var dopNumber = that.validation.dopErrors.length;
					if(number > 0){
						return that.validation.errors[0].message;
					}else if(dopNumber > 0){
						return that.validation.dopErrors[dopNumber - 1].message;
					}
				}
			},
			showMessage: function (text,type) {
				$().toastmessage('showToast', {
					text: text,
					stayTime:5000,
					type:type
				});
			}

        };

        return that;
    }

}(window, document, undefined, jQuery));

var post_add = Post_add();
post_add.initEvents();