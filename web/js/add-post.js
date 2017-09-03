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
						$(this).parents('.btn-selected-option').remove();
						that.selectorFields.closeSelector();
					});

				$(document).off('click', '.add-field-contact .option-select-field')
					.on('click', '.add-field-contact .option-select-field', function () {
						var caseField = $(this).data('value');
						var elemAfterInsert = $('#' + $(this).parents('.container-scroll').attr('id') + '-value').parents('.selected-field');
						that.contacts.init(elemAfterInsert);
						that.contacts.caseStart(caseField);
						$('#' + $(this).parents('.container-scroll').attr('id') + '-value').click();
					});

				$(document).off('click', '.close-input-contact')
					.on('click', '.close-input-contact', function () {
						$(this).parents('.block-input-contact').remove();
					});
				$(document).off('click','.select-all')
					.on('click', '.select-all', function () {
						that.workTime.clearBtns(true);
						that.workTime.selectedAll();
						$(this).addClass('active');

					});
				$(document).off('click','.unselect-all')
					.on('click', '.unselect-all', function () {
						that.workTime.clearBtns(true);
						$(this).addClass('active');
						that.workTime.unSelectedAll()
					})

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
							$content.find('input').attr('name',that.selectorFields.id+'[]');
							$block_inputs.html($content.clone());
							that.selectorFields.$inputSearch.attr('data-value',$(this).text());
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
					$container.mCustomScrollbar({scrollInertia: 50});
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

				}

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
					that.contacts.$inputContact = $('<input placeholder="Введите номер телефона">');
				},

				batchInsert: function () {
					that.contacts.$blockInputContact.append(that.contacts.$inputContact);
					that.contacts.$blockInputContact.append(that.contacts.$btnClose);
					that.contacts.$elemAfterInsert.before(that.contacts.$blockInputContact);
				},

				caseStart: function (caseField) {

					switch (caseField){
						case 'Телефон':{
							that.contacts.$blockInputContact.find('img').attr('src','img/icon-phone-min.png');
							that.contacts.$inputContact.attr('placeholder','Номер телефона');
							that.contacts.$inputContact.attr('name','contacts["phones"][]');
							that.contacts.batchInsert();
						}break;
						case 'Веб-сайт':{
							that.contacts.$blockInputContact.find('img').attr('src','img/icon-link-min.png');
							that.contacts.$inputContact.attr('placeholder','Ссылка на сайт');
							that.contacts.$inputContact.attr('name','contacts["web_site"][]');
							that.contacts.batchInsert();
						}break;
						case 'Вконтакте':{
							that.contacts.$blockInputContact.find('img').attr('src','img/icon-vk-min.png');
							that.contacts.$inputContact.attr('placeholder','Ссылка на страницу');
							that.contacts.$inputContact.attr('name','contacts["vk"][]');
							that.contacts.batchInsert();
						}break;
						case 'Одноклассники':{
							that.contacts.$blockInputContact.find('img').attr('src','img/icon-ok-min.png');
							that.contacts.$inputContact.attr('placeholder','Ссылка на страницу');
							that.contacts.$inputContact.attr('name','contacts["ok"][]');
							that.contacts.batchInsert();
						}break;
						case 'Twitter':{
							that.contacts.$blockInputContact.find('img').attr('src','img/icon-tw-min.png');
							that.contacts.$inputContact.attr('placeholder','Ссылка на страницу');
							that.contacts.$inputContact.attr('name','contacts["tw"][]');
							that.contacts.batchInsert();
						}break;
						case 'Facebook':{
							that.contacts.$blockInputContact.find('img').attr('src','img/icon-fb-min.png');
							that.contacts.$inputContact.attr('placeholder','Ссылка на страницу');
							that.contacts.$inputContact.attr('name','contacts["fb"][]');
							that.contacts.batchInsert();
						}break;
						case 'Instagram':{
							that.contacts.$blockInputContact.find('img').attr('src','img/icon-instagram-min.png');
							that.contacts.$inputContact.attr('placeholder','Ссылка на страницу');
							that.contacts.$inputContact.attr('name','contacts["instagram"][]');
							that.contacts.batchInsert();
						}break;

					}

				}
			},
			workTime: {
				selectedAll: function () {
					var $btn_input = $('.input-time-work-btn input');
					$btn_input.attr('value','select');
					$btn_input.val('select');
					$('.close-select-field').trigger('click');
					that.workTime.initInputEvents();
				},
				unSelectedAll: function () {
					var $btn_input = $('.input-time-work-btn input');
					$btn_input.attr('value','unselect');
					$btn_input.val('unselect');
					$('.close-select-field').trigger('click');
					that.workTime.initInputEvents();
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
				},
				initInputEvents:function () {
					$(document).off('keydown', '.container-time .time-period input')
						.on('keydown', '.container-time .time-period input', function () {
							that.workTime.clearBtns(false);
							$(document).off('keydown', '.container-time .time-period input');
						})
				}
			},


        };

        return that;
    }

}(window, document, undefined, jQuery));

var post_add = Post_add();
post_add.initEvents();