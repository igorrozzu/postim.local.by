var userSettings = (function (window, document, undefined, $) {

	return function () {
		const MAX_USER_PHOTO_SIZE = 5242880; //5mb
		var forms = {
			email: {
				storage: null
			},
		};
		var __$containerForms = $('.container-blackout-popup-window');
		var that = {

			selectBlockInit: function () {
				$(document).ready(function () {
					$(document).on('click', '.select-value', function () {
						$(this).next().click();
					});
					$(document).on('click', '.open-select-field', function () {
						var id_block = $(this).data('open-id');
						$(this).removeClass('open-select-field').addClass('close-select-field');
						$('#' + id_block).addClass('open-select');
					});

					$(document).on('click', '.close-select-field', function () {
						var id_block = $(this).data('open-id');
						$(this).removeClass('close-select-field').addClass('open-select-field');
						$('#' + id_block).removeClass('open-select');
					});

					$(document).on('click', '.option-active .option-select-field', function () {
						var prntId = $(this).parents('.container-scroll').attr('id');

						var selectValue = $(this).data('value');
						var selectLabel = $(this).html();

						var preSelectValue = $('#' + prntId + '-value').data('value');
						var preSelectLabel = $('#' + prntId + '-value').html();

						$('#' + prntId + '-value').data('value', selectValue);
						$('#' + prntId + '-value').html(selectLabel);
						$('#' + prntId + '-hidden').val(selectValue);

						$(this).data('value', preSelectValue);
						$(this).html(preSelectLabel);
						if (preSelectValue === '') {
							$(this).remove()
						}
						$('#' + prntId + '-value').click();
					})
				});
			},

			customScrollbarInit: function () {
				$(document).ready(function () {
					main.initCustomScrollBar($('.container-scroll-active'), {scrollInertia: 50});
				});
			},

			uploadUserPhotoHandler: function () {
				$(document).off('change', '#user-photo').on('change', '#user-photo', function (e) {
					var file = e.target.files[0];
					if (that.validateUploadUserPhoto(file)) {
						var form = new FormData();
						form.append('user-photo', file);
						$.ajax({
							url: '/user/upload-photo',
							type: "POST",
							data: form,
							cache: false,
							processData: false,
							contentType: false,
							dataType: 'json',
							success: that.successUploadingUserPhotoHandler
						});
					}
					$(this).val('');
				});
			},
			validateUploadUserPhoto: function (file) {
				var regexValidFormat = /(image\/jpeg)|(image\/png)|(image\/gif)/;
				if (!regexValidFormat.test(file.type) || file.size > MAX_USER_PHOTO_SIZE) {
					that.errorUploadingUserPhotoHandler('Изображение должно быть не меньше, чем 300 x 300 ' +
							'пикселей в формате JPG, GIF или PNG. ' +
							'Макс. размер файла: 5 МБ.');
					return false;
				}
				return true;
			},
			successUploadingUserPhotoHandler: function (response) {
				if (response.success) {
					$('.user-icon-profile img').attr('src', response.pathToPhoto);
					$('.profile-icon-menu img').attr('src', response.pathToPhoto);
					$('.user_icon img').attr('src', response.pathToPhoto);
					$().toastmessage('showToast', {
						text: response.message,
						stayTime: 5000,
						type: 'success'
					});
				} else {
					that.errorUploadingUserPhotoHandler(response.message);
				}
			},
			errorUploadingUserPhotoHandler: function (message) {
				$().toastmessage('showToast', {
					text: message,
					stayTime: 5000,
					type: 'error'
				});
			},
			changeEmailHandler: function () {
				$(document).on('click', '.сhange-email-btn', function () {
					forms.email.storage = forms.email.storage || that.getChangeEmailForm();
					__$containerForms.html(forms.email.storage).show();
				});

				$(document).on('click', '#change-email-btn', function () {
					var form = $('#change-email-form').serialize();
					$.post('/user/change-email', form, function (response) {
						__$containerForms.html(response);
					})
				});
			},
			initPjaxEvents: function () {
				$(document).on('pjax:end', function (data, status, xhr, options) {
					that.customScrollbarInit();
				});
			},
			getChangeEmailForm: function () {
				var rez = null;
				$.ajax({
					url: '/user/change-email',
					type: "GET",
					async: false,
					success: function (response) {
						rez = response;
					}
				});
				return rez;

			},

			initHandlers: function () {
				that.uploadUserPhotoHandler();
				that.changeEmailHandler();
				that.initPjaxEvents();
			},


		};
		return that;
	}

}(window, document, undefined, jQuery));

var user_settings = userSettings();
user_settings.customScrollbarInit();
user_settings.selectBlockInit();
user_settings.initHandlers();

