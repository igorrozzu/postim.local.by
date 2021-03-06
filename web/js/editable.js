var Editable = (function (window, document, undefined,$) {

	return function () {
		var __editor = null;
		var that = {
			$containerEditor : null,
			$containerToolbar : $('<div class="container-toolbar"> <div class="title-toolbar">Текст</div> <div class="btns-toolbar-container"><div class="btn-toolbar-top"></div> <div class="btn-toolbar-down"></div> <div class="btn-toolbar-close"></div></div></div>'),
			$containerInsert : $('<div class="item-editor item"></div>'),
			$blockInsert : $('<div class="block-insert"></div>'),
			$blockAction : $('<div class="block-action"></div>'),

			toolbar: {
				video:false,
				photo:false,
				text:false,
			},

			init:function (selector,params,selectorContainer) {

				that.initEditor(selector);
				that.initParams(params);

				that.$containerEditor = $('.block-write-editors');
				if (selectorContainer != undefined) {
					that.$containerEditor = $(selectorContainer);
				}

				that.renderButtons();
				that.initEventsToolbar();
			},
			initEditor:function (selector) {
				__editor = new MediumEditor(selector,{
					toolbar: {
						/* These are the default options for the toolbar,
						 if nothing is passed this is what is used */
						allowMultiParagraphSelection: true,
						buttons: [
							{
								name: 'bold',
								action: 'bold',
								aria: 'Жирный',
								tagNames: ['b', 'strong'],
								style: {
									prop: 'font-weight',
									value: '700|bold'
								},
								useQueryState: true,
								contentDefault: '<b>B</b>',
								contentFA: '<i class="fa fa-bold"></i>'
							},
							{
								name: 'italic',
								action: 'italic',
								aria: 'Курсив',
								tagNames: ['i', 'em'],
								style: {
									prop: 'font-style',
									value: 'italic'
								},
								useQueryState: true,
								contentDefault: '<b><i>I</i></b>',
								contentFA: '<i class="fa fa-italic"></i>'
							},
							'anchor',
							{
								name: 'h2',
								action: 'append-h2',
								aria: 'Заголовок второго уровня',
								tagNames: ['h2'],
								contentDefault: '<b>H2</b>',
								contentFA: '<i class="fa fa-header"><sup>2</sup>'
							},
							{
								name: 'quote',
								action: 'append-blockquote',
								aria: 'Цитата',
								tagNames: ['blockquote'],
								contentDefault: '<b>&ldquo;</b>',
								contentFA: '<i class="fa fa-quote-right"></i>'
							}
						],

						diffLeft: 0,
						diffTop: -10,
						firstButtonClass: 'medium-editor-button-first',
						lastButtonClass: 'medium-editor-button-last',
						relativeContainer: null,
						standardizeSelectionStart: false,
						static: false,
						align: 'center',
						sticky: false,
						updateOnEmptySelection: false
					},
					anchor: {
						aria: 'Ссылка',
						linkValidation: true,
						placeholderText: 'Вставьте или введите ссылку'
					},
					placeholder: {
						text: 'Введите текст',
						hideOnClick: true
					}
				});
			},
			initParams: function (params) {

				for(var name in params){
					that[name] = params[name];
				}
			},
			initEventsToolbar: function () {
				$(document).off('click','.btn-toolbar-top')
					.on('click','.btn-toolbar-top',function () {
						var $parent = $(this).parents('.item-editor');
						if($parent.prev().hasClass('item-editor')){
							$parent.prev().before($parent);
						}else {
							if($parent.prev().prev().hasClass('item-editor')){
								$parent.prev().prev().before($parent);
							}
						}

				});

				$(document).off('click','.btn-toolbar-down')
					.on('click','.btn-toolbar-down',function () {
					var $parent = $(this).parents('.item-editor');
					if($parent.next().hasClass('item-editor')){
						$parent.next().after($parent);
					}else {
						if($parent.next().next().hasClass('item-editor')){
							$parent.next().next().after($parent);
						}
					}

				});

				$(document).off('click','.btn-toolbar-close')
					.on('click','.btn-toolbar-close',function () {
						var $parent = $(this).parents('.item-editor');
						$parent.remove();
				});

				$(document).off('click','.--mini-icon-photo')
					.on('click','.--mini-icon-photo',function () {
						that.photo.renderBlockForInsert.call(this);
                    });

                $(document).off('change','.photo-editable-add')
					.on('change','.photo-editable-add',function (e) {
                    that.photo.addPhotos.call(this, e);
                });

                $(document).off('click','.__block-action_btns_photo')
					.on('click','.__block-action_btns_photo',function () {
						that.photo.$containerReplace = $(this).parents('.container-insert');
                    });

				$(document).off('click','.btn-write-description.btn-play-min')
					.on('click','.btn-write-description.btn-play-min',function () {
						var $container = that.$containerInsert.clone();
						var $containerToolbar = that.$containerToolbar.clone();
						$containerToolbar.find('.title-toolbar').text('Видео');

						$container.addClass('container-insert');
						$container.append($containerToolbar.clone());

						var $blockInsert = that.$blockInsert.clone();
						var $blockAction = that.$blockAction.clone();

						$blockAction.append('<input class="input-video" type="text" placeholder="Вставте ссылку на видео">');
						$blockAction.append('<p>Вы можете указать ссылку на страницу видеозаписи на таких сайтах, как Youtube, Rutube, Vimeo, Coub</p>');

						$blockInsert.append($blockAction);
						$container.append($blockInsert);
						$(this).parents('.btns-write-description').before($container);

				});

				$(document).off('click','.btn-write-description.btn-text')
					.on('click','.btn-write-description.btn-text',function () {
						var $container = that.$containerInsert.clone();
						var $containerToolbar = that.$containerToolbar.clone();

						$containerToolbar.find('.title-toolbar').text('Текст');

						$container.addClass('container-editor');
						$container.append($containerToolbar.clone());
						$container.append($('<div class="editable"></div>'));
						$(this).parents('.btns-write-description').before($container);
						that.initEditor('.editable');
				});

				$(document).off('click','.item-editor,.item-editor-default')
					.on('click','.item-editor,.item-editor-default',function (e) {
						if ($(e.target).parents('.container-toolbar').length) {
							return false;
						} else {
							$(this).after($('.btns-write-description'));
						}

				});

				$(document).off('paste','.input-video')
					.on('paste','.input-video',function () {
						$input = $(this);
						setTimeout(function () {
							var linkToTheVideo = $input.val();
							that.video.$containerInsert = $input.parents('.block-insert');
							that.video.insertVideoByUrl(linkToTheVideo);
						}, 10);
				});

				$(document).off('paste','.input-photo')
					.on('paste','.input-photo',function () {
						var $input = $(this);
                        that.photo.$containerReplace = $(this).parents('.container-insert');
						setTimeout(function () {
							var linkToPhoto = $input.val();
							var url = $('#article').data('upload-by-url');
                            uploads.uploadByURL(url, linkToPhoto, that.photo.renderPhotos);
                        },10)
                    })

			},
			renderButtons: function () {
				var $btnsContainer = $('<div class="btns-write-description"> </div>');
				var btns = {
					video : $('<div class="btn-write-description btn-play-min">Видео</div>'),
					photo : $('<div class="btn-write-description --mini-icon-photo">Фото</div>'),
					text: $('<div class="btn-write-description btn-text">Текст</div>'),
				};

				var foo =false;

				for(var btnsName in that.toolbar){
					if(that.toolbar[btnsName]){
						foo = true;
						$btnsContainer.append(btns[btnsName]);
					}
				}
				if(foo){
					that.$containerEditor.append($btnsContainer);
				}
			},
			photo: {
				$containerReplace : null,
				$blockPhoto : $('<div class="photo-item"><img><input placeholder="Подпись к фото" class="img-source"></div>'),

                renderBlockForInsert: function () {

                    var $container = that.$containerInsert.clone();
                    var $containerToolbar = that.$containerToolbar.clone();
                    $containerToolbar.find('.title-toolbar').text('Фото');

                    $container.addClass('container-insert');
                    $container.append($containerToolbar.clone());

                    var $blockInsert = that.$blockInsert.clone();
                    var $blockAction = that.$blockAction.clone();

                    $blockAction.append('<input class="input-photo" type="text" placeholder="Вставте ссылку на изображение">');
                    $blockAction.append('<div class="__block-action_btns_photo">' +
						'<label><span>Загрузить изображение</span>' +
						'<input style="display: none" class="photo-editable-add" name="photo-editable-add" type="file" multiple' +
                        '               accept="image/*,image/jpeg,image/gif,image/png">' +
						'</label></div>');

                    $blockInsert.append($blockAction);
                    $container.append($blockInsert);
                    $(this).parents('.btns-write-description').before($container);

                },

                addPhotos: function (e) {
                    if (uploads.validatePhotos(e.target.files)) {
                        var form = new FormData();
                        $.each(e.target.files, function (key, value) {
                            form.append('photos[]', value);
                        });

                        var url = $('#article').data('upload-by-file');
                        uploads.uploadFiles(url, form, that.photo.renderPhotos);
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
                    if(response.success){

                        var number = response.data.length;
                        console.log(response);

                        for (var i = 0; i < number; i++) {

                            var $container = that.$containerInsert.clone();
                            var $containerToolbar = that.$containerToolbar.clone();
                            $containerToolbar.find('.title-toolbar').text('Фото');

                            $container.addClass('container-insert');
                            $container.append($containerToolbar.clone());

                            var $blockInsert = that.$blockInsert.clone();
							var $blockPhoto = that.photo.$blockPhoto.clone();

							$blockPhoto.find('img').attr('src',response.data[i].link);

							$blockInsert.html($blockPhoto);
							$blockInsert.addClass('js-photo');
							$container.append($blockInsert);
                            that.photo.$containerReplace.before($container);
                        }
                        that.photo.$containerReplace.remove();
                        that.photo.$containerReplace = null;

                    }else {
                        $().toastmessage('showToast', {
                            text     : response.message,
                            stayTime:  5000,
                            type     : 'error'
                        });
                    }
                }
			},
			video: {
				$containerInsert:null,
				insertVideoByUrl:function (url) {
					//youtube
					var RegExpLink = /(https:\/\/www.youtube.com\/watch\?v=.*?)|(https:\/\/youtu.be\/.*?)/;
					if (RegExpLink.test(url)) {

						var idToTheVideo = url.replace(RegExpLink, '');
						that.video.renderVedio($('<iframe  type="text/html" src="https://www.youtube.com/embed/' + idToTheVideo + '?autoplay=0&origin=https://'+window.location.host+'" frameborder="0" allowfullscreen/>'));
					}

					//rutube
					var RegulRutube=/(https?:\/\/rutube.ru\/video\/.*?)/;
					if(RegulRutube.test(url)){
						var apiRequestRutube='http://rutube.ru/api/oembed/?url='+url+'&format=json';
						that.video.renderVedio(that.video.getVideo(apiRequestRutube));
					}

					//vimeo
					var RegulVimeo=/(https:\/\/vimeo.com\/channels\/staffpicks\/.*?)|(https:\/\/vimeo.com\/.*?)/;
					if(RegulVimeo.test(url)){
						var apiRequestVimeo='http://vimeo.com/api/oembed.json?url='+url;
						that.video.renderVedio(that.video.getVideo(apiRequestVimeo));
					}

					//coub
					var RegulCoub=/(http:\/\/coub.com\/view\/.*?)|(https:\/\/coub.com\/view\/.*?)/;
					if(RegulCoub.test(url)){
						var idVideo=url.replace(RegulCoub,'');
						var apiRequestCoub="http://coub.com/api/oembed.json?url="+url+"&autoplay=false&maxwidth=691";
						that.video.renderVedio(that.video.getVideo(apiRequestCoub));
					}

				},
				getVideo: function (query) {
					var html = '';

					$.ajax({
						url: '/post/get-code-video',
						type: "get",
						dataType: "json",
						async: false,
						data: {
							query: query
						},
						success: function (response) {
							if (response.provider_name == 'Coub') {
								html = response.html;
								html = html.replace(/autoplay=true/g, 'autoplay=false');
							} else {
								response.html = response.html.replace(/width=".*?"/, '');
								response.html = response.html.replace(/height=".*?"/, '');
								html = response.html;
							}
						}
					});

					return html;
				},
				renderVedio:function ($tag) {
					that.video.$containerInsert.addClass('video');
					that.video.$containerInsert.css('display','block');
					that.video.$containerInsert.html($tag);
				}
			},
			parserEditable: function () {
				if(that.validation.validBeforeSend()){
					var $tmpBlock = $('<div></div>');
					var is_insert = false;

					$('.block-write-editors .item').each(function () {
						var $tmpInsert = $('<div class="insert-item"></div>');

						if($(this).hasClass('container-editor')){
							var str = $(this).find('.editable').html();
							str = str.replace(/<[^>]+>/g,'');
							if(str.length > 5){
								$tmpInsert.html($(this).find('.editable').html());
								$tmpBlock.append($tmpInsert.clone());
								is_insert = true;
							}
						}
						if($(this).hasClass('container-insert')){
							var $tmpBlockInsert = $($(this).find('.block-insert').html());
							if(!$tmpBlockInsert.hasClass('block-action')){
								$tmpInsert.html($(this).find('.block-insert').html());

								if($(this).find('.block-insert').hasClass('video')){
									$tmpInsert.addClass('video');
								}

								if($(this).find('.block-insert').hasClass('js-photo')){
									var textFromInput = $(this).find('.block-insert').find('input').val();
                                    $tmpInsert.find('input').remove();
                                    if(textFromInput){
                                        $tmpInsert.find('.photo-item').append('<div class="photo-desc">'+textFromInput+'</div>')
									}
                                    $tmpInsert.find('.photo-item').removeClass('photo-item').addClass('block-photo-post');
								}

								$tmpBlock.append($tmpInsert.clone());
								is_insert = true;
							}
						}
					});
					var $article = $('#article');
					if(is_insert){
						$article.val($tmpBlock.html());
						$article.attr('value',$tmpBlock.html());
					}else {
						$article.val('');
						$article.attr('value','');
					}
					return true;
				}else {
					return false;
				}

			},
			validation:{
				requiredDefaultText:{active:false, message:''},
				messages:[],
				is_valid:true,
				validBeforeSend:function () {
					$('.block-write-editors .item').each(function () {
						that.validation.messages = [];
						that.validation.is_valid = true;
						if($(this).hasClass('container-editor') && $(this).hasClass('item-editor-default') && that.validation.requiredDefaultText.active){
							if($(this).find('.editable').html().length < 5){
								that.validation.addError({$elem:$(this),message:that.validation.requiredDefaultText.message});
							}
						}


					});
					return that.validation.is_valid;
				},
				addError:function (error) {
					that.validation.messages.push(error);
					that.validation.is_valid = false;
				}
			}

		};

		return that;
	}

}(window,document,undefined,jQuery));

var editable = Editable();
