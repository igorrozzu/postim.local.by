var BusinessAccount = (function (window, document, undefined, $) {

	return function () {
		var methods = {
			statisticSearchByPromoCode: function () {
				var search_field = $(this).val();
				var $table = $('.table-container');

				$.ajax({
					url: $(this).data('href') + '&search_field=' + search_field,
					type: "GET",
					async: true,
					success: function (response) {
						$table.html(response);
					}
				});
			}
		};
		var that = {

			init: function () {
				$(document).ready(function () {
					that.statisticSearchByPromoCodeInitHandler();
					that.pinCodeShowBtnHandler();
					that.statisticTableScrollInit();
					that.confirmOrderHandler();
				});
			},

			statisticSearchByPromoCodeInitHandler: function () {
				$(document).off('input', '.search-by-promo-code')
						.on('input', '.search-by-promo-code', $.debounce(500, methods.statisticSearchByPromoCode));
			},

			pinCodeShowBtnHandler: function () {
				$(document).on('click', '.text-pin2', function () {
					if (!$(this).parents('.block-promo-pin').hasClass('active')) {
						$('.block-promo-pin.active').removeClass('active');
						$(this).parents('.block-promo-pin').addClass('active');
					}

				});
				$(document).click(function (e) {
					if ($(e.target).closest(".block-promo-pin").length) return;
					$('.block-promo-pin.active input').val('');
					$('.block-promo-pin.active').removeClass('active');
				})
			},

			statisticTableScrollInit: function () {
				main.initCustomScrollBar($('.horizontal-scroll12'), {axis: "x", scrollInertia: 50})
			},

			confirmOrderHandler: function () {
				$(document).on('click', '.btn-enter-pin,.close-promocode', function (e) {
					console.log(e.target);
					var $button = $(this);
					var id = $button.data('id');
					var type = $button.data('type');
					var data = 'id=' + id;
					if (type === 'certificate') {
						var code = $button.prev().val();
						if (code !== '') {
							data += '&code=' + code;
						} else {
							return false;
						}
					}
					$.ajax({
						url: '/business-account/confirm-success-order',
						type: "POST",
						data: data,
						success: function (response) {
							var type = 'error';
							var text = 'Неверный пин-код';
							var replace_button = '<span class="confirm-order-btn"></span>';
							if (response.success) {
								type = 'success';
								text = 'Заказ успешно обработан';
								if ($button.hasClass('close-promocode')) {
									$button.replaceWith(replace_button);
								} else {
									$button.parent().replaceWith(replace_button);
								}
							}
							$().toastmessage('showToast', {
								text: text,
								stayTime: 5000,
								type: type
							});
						}
					});
				});
			},

			Account: function () {

				var scope = {
					init: function () {
						$(document).off('click', '.make-payment')
								.on('click', '.make-payment', function () {
									if ($(this).hasClass('disable')) {
										return;
									}

									$('#payment-form-entity').val($(this).data('entity'));
									$('#account-form').submit();
								});
					}
				};

				return scope;
			}
		};

		return that;
	}

}(window, document, undefined, jQuery));

var businessAccount = BusinessAccount();
businessAccount.init();
businessAccount.Account().init();

