/**
 * Created by jrborisov on 8.7.17.
 */
var Search = (function (window, document, undefined, $) {

	return function () {

		var __$containerAutoComplete = null;
		var __$body = null;
		var config = {
			minTextLength: 1,
		}

		var that = {

			init: function () {

				that.initEvents();

				__$containerAutoComplete = $('.block-auto-complete-search');
				__$body = $('body');

			},

			initEvents: function () {

				$(document).ready(function () {

					$(document).off('click', '.search')
							.on('click', '.search', function () {
								if (!__$body.hasClass('open-search')) {
									__$body.addClass('open-search');
									that.getAutoComplete.call($(this));
								}

							});

					$(document).off('click', '.btn-search')
							.on('click', '.btn-search', function () {
								if (__$body.hasClass('open-search')) {
									if ($('.search').val()) {
										window.location.href = '/search/' + $('.search').val();
									}
								} else {
									__$body.addClass('open-search');
									that.getAutoComplete.call($(this));
								}


							});

					$(document).off('click', '.search_block .cancel')
							.on('click', '.search_block .cancel', function () {
								that.closeSearch();
							});

					$(document).off('keydown', '.search').on('keydown', '.search', function (e) {
						if (e.keyCode == 13 && $('body').hasClass('open-search') && $('.search').val()) {
							window.location.href = '/search/' + $('.search').val();
						} else {
							that.getAutoComplete.call($(this));
						}

					});

					$(document).off('click', '.container-body-auto-complete a')
							.on('click', '.container-body-auto-complete a', function () {
								$('.search').val('');
								that.closeSearch();
							})
				});
			},

			closeSearch: function () {
				$('body').attr('class', '');
				$('.block-auto-complete-search.active').removeClass('active');
			},

			clear: function () {
				$('.search').val('');
			},

			getAutoComplete: function () {
				var $input = this;

				$('body').off('click').on('click', function (e) {
					if ($(e.target).closest(".search_block,.block-auto-complete-search").length) return;
					that.closeSearch();
				})

				setTimeout(function () {

					var text = $input.val();
					if (text.length > config.minTextLength) {
						$.get('/main-search/auto-complete', {text: text}, function (html) {
							if (!!html) {
								__$containerAutoComplete.html(html);
								if (!__$containerAutoComplete.hasClass('active')) {
									__$containerAutoComplete.addClass('active');
								}
							} else {
								__$containerAutoComplete.removeClass('active')
							}

						})
					} else {
						__$containerAutoComplete.removeClass('active')
					}


				}, 10)
			}

		}

		return that;
	}

}(window, document, undefined, jQuery));

var search = Search();
search.init();

