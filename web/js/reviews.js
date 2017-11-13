
var Reviews = (function (window, document, undefined,$) {

	return function () {

		var __$container_write_review = $(
			'<div class="container-write-reviews"> ' +
			'<form class="form-write-reviews">' +
				'<input type="hidden" id="input_reviews_post_id" name="reviews[post_id]">'+
				'<div class="add-review-label mark">Поставьте вашу оценку</div> ' +
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
					'<textarea name="reviews[data]" placeholder="Пожалуйста, аргументируйте свою оценку. Напишите не менее 100 символов. Расскажите в деталях о своем опыте. Что заслуживает отдельного внимания? Рекомендуете или нет?" class="textarea-review"></textarea> ' +
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
						if(!main.User.is_guest){
							that.openWriteContainer.apply(this);
						}else {
							main.showErrorAut('Неавторизованные пользователи не могут оставлять отзывы');
						}
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
						that.sendReviews.call(this);
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
					});

				$(document).off('click','.review-footer-btn.btn-like')
					.on('click','.review-footer-btn.btn-like',function () {
						if(!main.User.is_guest){
							that.setLike.apply(this);
						}else {
							main.showErrorAut('Неавторизованные пользователи не могут оценивать отзывы');
						}

					});

				$(document).off('click','.review-footer-btn.btn-complaint')
					.on('click','.review-footer-btn.btn-complaint',function () {
						if(!main.User.is_guest){
							that.showFormComplaint.apply(this);
						}else {
							main.showErrorAut('Неавторизованные пользователи не могут оставлять жалобы');
						}

					});

				$(document).off('click','.review-footer-btn.btn-comm')
					.on('click','.review-footer-btn.btn-comm',function () {
						that.openCommentsReviews.apply(this);
					});

				$(document).off('click','.review-footer-btn.hide-comm')
					.on('click','.review-footer-btn.hide-comm',function () {
						that.closeCommentsReviews.apply(this);
					});

				$(document).off('click','.review-footer-btn.btn-edit-reviews')
					.on('click','.review-footer-btn.btn-edit-reviews',function () {
						that.getEditFormReviews.apply(this);
					});


			},

			sendReviews:function () {
				var formData = $(this).parents('.block-write-reviews')
					.find('.form-write-reviews')
					.serialize();

				$.post('/site/save-reviews', formData, function (response) {
					if(response.success){

						if(!!!response.html){
							$('.menu-btns-card .btn2-menu.active').parents('a').trigger('click');
						}else {
							$('.block-write-reviews.active.edit_reviews').replaceWith(response.html);
							$('.block-reviews.without_header.hide').remove();
							that.closeFormReviews();
						}
						$().toastmessage('showToast', {text: response.message, stayTime:5000, type: 'success'});

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
				that.closeFormReviews();
				$(this).parents('.block-write-reviews').addClass('active')
					.find('.container-write-reviews')
					.html(__$container_write_review.html());

				$(this).removeClass('open-container').addClass('btn-send-reviews');
				$(this).find('p').text('Опубликовать');

				var post_id = $(this).parents('.block-write-reviews').attr('data-post_id');

				$('#input_reviews_post_id').attr('value',post_id).val(post_id);
				var scrollTop = $('.block-write-reviews').offset().top;
				$(document).scrollTop(scrollTop);
				$('.block-textarea-review textarea').autosize();
			},

			setLike:function () {

				var $btn_like = $(this);

				var object_send={
					id:null
				};

				object_send.id=$btn_like.parents('.block-reviews').attr('data-reviews_id');


				$.ajax({
					url: '/site/add-remove-like-reviews',
					type: "GET",
					dataType: "json",
					data:object_send,
					success: function (response) {
						if(response.status=='error'){
							$().toastmessage('showToast', {
								text: response.message,
								stayTime:5000,
								type:'error'
							});

						}else {
							if(response.status=='add'){
								$btn_like.addClass('active').text(response.count);
							}else {
								$btn_like.removeClass('active').text(response.count);
							}
						}
					}
				});
			},

			setMark:function () {
				var value = Number($(this).text());
				var valueText = '';

				$('.container-evaluations .evaluation.active').removeClass('active');
				$(this).addClass('active');
				$('#input-evaluation').attr('value',value).val(value);

				switch (value){
					case 1:{valueText = 'Очень плохо';}break;
					case 2:{valueText = 'Не понравилось';}break;
					case 3:{valueText = 'Нормально';}break;
					case 4:{valueText = 'Хорошо';}break;
					case 5:{valueText = 'Отлично';}break;
				}

				$('.add-review-label.mark').text(valueText);
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
			},

			showFormComplaint:function () {
				var $btn_complaint = $(this);
				var id_reviews = $btn_complaint.parents('.block-reviews').attr('data-reviews_id');
				var type_reviews = 2;

                main.initFormComplaint(id_reviews,type_reviews,function () {
                    $btn_complaint.remove();
                });

			},

			openCommentsReviews:function () {

				var $btn_comment = $(this);
				$btn_comment.addClass('hide-comm').removeClass('btn-comm').text('Скрыть');

				var id_reviews = $btn_comment.parents('.block-reviews').attr('data-reviews_id');
				var $containerForComments = $btn_comment.parents('.block-reviews').find('.container-reviews-comments');

				$.get('/post/get-reviews-comments',{id:id_reviews},function (html) {
					$containerForComments.html(html);
					comments.init(2);
					comments.setAutoResize('.textarea-main-comment');
					var scrollTop = $containerForComments.offset().top-100;
					$(document).scrollTop(scrollTop);
				});



			},

			closeCommentsReviews:function () {
				var $btn_comment = $(this);
				$btn_comment.removeClass('hide-comm')
					.addClass('btn-comm')
					.text($btn_comment.data('text')==0?'Ответить':$btn_comment.data('text'));

				var $containerForComments = $btn_comment.parents('.block-reviews').find('.container-reviews-comments');
				$containerForComments.html('');

			},

			getEditFormReviews:function () {
				var $container = $(this).parents('.block-reviews');
				var id_reviews = $container.attr('data-reviews_id');

				$.ajax({
					url:'/post/get-reviews-edit',
					type: "GET",
					data: {id:id_reviews},
					dataType: 'json',
					success: function (response) {
						if(!!response.success){
							that.closeFormReviews();
							$container.addClass('hide');
							$container.before(response.html);
							var scrollTop = $container.prev().offset().top-100;
							$(document).scrollTop(scrollTop);
						}
					}
				});
			},


			closeFormReviews:function () {
				$('.block-write-reviews')
					.find('.container-write-reviews')
					.html('');

				$('.btn-send-reviews').removeClass('btn-send-reviews')
					.addClass('open-container')
					.find('p').text('Написать новый отзыв');

				$('.block-write-reviews.active.edit_reviews').remove();
				$('.block-reviews.hide').removeClass('hide');
			},

			scrollToFirstReviews:function () {
				setTimeout(function () {
                    var scrollTop = $('.block-reviews').offset().top - 100;
                    $(document).scrollTop(scrollTop);
                },500);

            }

		}

		return that;
	}

}(window,document,undefined,jQuery));

var reviews = Reviews();
reviews.init();
