
var Reviews = (function (window, document, undefined,$) {

	return function () {

		var __$container_write_review = $(
			'<div class="container-write-reviews"> ' +
			'<form id="form-write-reviews">' +
				'<input type="hidden" id="input_reviews_post_id" name="reviews[post_id]">'+
				'<div class="add-review-label">Поставте вашу оценку</div> ' +
				'<div class="container-evaluations"> ' +
					'<input type="hidden" id="input-evaluation" name="reviews[rating]">'+
					'<div class="evaluation">1</div> ' +
					'<div class="evaluation">2</div> ' +
					'<div class="evaluation">3</div> ' +
					'<div class="evaluation">4</div> ' +
					'<div class="evaluation">5</div> ' +
				'</div> ' +
				'<div class="add-review-label">Напишите отзыв</div> ' +
				'<div class="block-textarea-review"> ' +
					'<textarea name="reviews[data]" placeholder="Пожалуйста, аргументируйте свою оценку. Напишите не менее 100 символов." class="textarea-review"></textarea> ' +
					'<div class="container-insert-photos">' +
						'<div class="container-photos-inputs"></div>'+
						'<div class="block-tmp-photos"></div>'+
					'</div> ' +
					'<div class="block-btns-textarea-review"> ' +
						'<div class="btn-add-photo-review"><p>Добавить фото</p></div> ' +
						'<div class="btn-rule"><a href="#">Правила размещения отзывов</a></div>' +
					'</div> ' +
				'</div> ' +
			'</form>'+
			'</div>'
			);


		var that = {

			init:function () {

				$(document).off('click','.open-container')
					.on('click','.open-container',function () {
						that.openWriteContainer.apply(this);
					});

				$(document).off('click','.container-evaluations .evaluation')
					.on('click','.container-evaluations .evaluation',function () {
						that.setMark.apply(this);
					});

				$(document).off('change', '.photo-add-review')
					.on('change', '.photo-add-review', function (e) {
						that.addPhoto.call(this,e);
					});

				$(document).off('click','.btn-add-photo-review')
					.on('click','.btn-add-photo-review',function () {
						$('.photo-add-review').trigger('click');
					});

				$(document).off('click','.close-add-photo')
					.on('click','.close-add-photo',function () {
						that.deletePhoto($(this).parents('.review-photo-tmp').attr('id'));
					});

				$(document).off('click','.btn-send-reviews')
					.on('click','.btn-send-reviews',function () {
						that.sendReviews();
					});

				$(document).off('click','.switch-reviews')
					.on('click','.switch-reviews',function () {
						$.pjax.reload({
							container: '#post-feeds',
							url: $('.menu-btns-card a:eq(2)').attr('href'),
							push: true,
							replace: true,
							scrollTo:0
						});
					})
			},

			sendReviews:function () {
				var formData = $('#form-write-reviews').serialize();

				$.post('/site/save-reviews', formData, function (response) {
					if(response.success){
						$().toastmessage('showToast', {text: response.message, stayTime:5000, type: 'success'});

						var post_id = $('.block-write-reviews').attr('data-post_id');
						$('.menu-btns-card .btn2-menu.active').parents('a').trigger('click');

					}else {
						$().toastmessage('showToast', {
							text     : response.message,
							stayTime:  5000,
							type     : 'error'
						});
					}
				})
			},

			openWriteContainer:function () {
				$(this).parents('.block-write-reviews').addClass('active')
					.find('.container-write-reviews')
					.html(__$container_write_review.html());

				$(this).removeClass('open-container').addClass('btn-send-reviews');
				$(this).find('p').text('Опубликовать');

				var post_id = $(this).parents('.block-write-reviews').attr('data-post_id');

				$('#input_reviews_post_id').attr('value',post_id).val(post_id);
				//TODO проскролить до написания отзыва
				$('.block-textarea-review textarea').autosize();
			},

			setMark:function () {
				var value = $(this).text();

				$('.container-evaluations .evaluation.active').removeClass('active');
				$(this).addClass('active');
				$('#input-evaluation').attr('value',value).val(value);
			},

			addPhoto:function (e) {
				if (uploads.validatePhotos(e.target.files)) {
					var form = new FormData();
					$.each(e.target.files, function (key, value) {
						form.append('photos[]', value);
					});
					uploads.uploadFiles('/post/upload-tmp-photo', form, that.renderPhotos);
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

			renderPhotos:function (response) {

				var $block_photo = $('<div id="" class="review-photo-tmp" style="background-image: url(\'testP.png\')"><div class="close-add-photo"></div></div>');
				var $blockInput = $('<input id="input_..." style="display: none" name="reviews[photos][]" type="text">');
				var $containerNewPhoto = $('<div></div>');
				var $containerBlockInputs = $('<div></div>');

				if(response.success){
					var number = response.data.length;

					for (var i = 0; i < number; i++) {
						$block_photo.css('background-image', 'url("/post_photo/tmp/' + response.data[i].link + '")');
						$block_photo.attr('id', main.getHashCode(response.data[i].link));
						$containerNewPhoto.append($block_photo.clone());

						$blockInput.attr('id', 'inputs_'+main.getHashCode(response.data[i].link))
							.attr('value',response.data[i].link)
							.val(response.data[i].link);

						$containerBlockInputs.append($blockInput.clone());
					}

					$('.block-tmp-photos','.container-insert-photos').append($containerNewPhoto.html());
					$('.container-photos-inputs','.container-insert-photos').append($containerBlockInputs.html());

				}else{
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
			}

		}

		return that;
	}

}(window,document,undefined,jQuery));

var reviews = Reviews();
reviews.init();
