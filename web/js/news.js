/**
 * Created by jrborisov on 8.7.17.
 */
var News = (function (window, document, undefined, $) {

	return function () {
		var methods = {
			defineUrlByItemType: function (type) {
				switch (type) {
					case 'post' :
						return '/post/favorite-state';
						break;
					case 'news' :
						return '/news/favorite-state';
						break;
				}
			}
		};
		var that = {

			init: function () {
				$(document).ready(function () {
					that.addToFavorite();
					that.transitionToNewsHandler();
				});
			},
			addToFavorite: function () {
				$(document).on('click', '.bookmarks-btn', function () {
					if (main.User.is_guest) {
						main.showErrorAut('Неавторизованные пользователи не могут сохранять в Избранное понравившиеся');
						return false;
					}
					var $container_replace = $(this);
					var $block = $container_replace.closest('.card-block');
					var item_id = $block.data('item-id');
					var url = methods.defineUrlByItemType($block.data('type'));

					that.sendRequsetForStateItem($container_replace, url, item_id);
					return false;
				});

				$(document).on('click', '.block-info-reviewsAndfavorites .add-favorite', function () {
					if (main.User.is_guest) {
						main.showErrorAut('Неавторизованные пользователи не могут сохранять в Избранное понравившиеся');
						return false;
					}
					var $container_replace = $(this);
					var $block = $container_replace.closest('.block-info-reviewsAndfavorites');
					var item_id = $block.data('item-id');
					var url = methods.defineUrlByItemType($block.data('type'));
					that.sendRequsetForStateItem($container_replace, url, item_id);
				})
			},

			transitionToNewsHandler: function () {
				$(document).off('click', '.js-href-news').on('click', '.js-href-news', function (e) {
					$(this).parent().find('.main-pjax a').trigger('click');
				});
			},

			sendRequsetForStateItem: function ($container_replace, url, item_id, insert_count) {
				insert_count = insert_count === undefined ? true : false;

				$.ajax({
					url: url,
					type: 'POST',
					data: {itemId: item_id},
					dataType: "json",
					async: false,
					success: function (response) {
						if (response.status == 'error') {
							$().toastmessage('showToast', {
								text: response.message,
								stayTime: 5000,
								type: 'error'
							});

						} else {
							if (insert_count) {
								$container_replace.text(response.count);
							}
							if (response.status == 'add') {
								$container_replace.addClass('active');
							} else {
								$container_replace.removeClass('active');
							}
						}
					}
				});
			},
		}

		return that;
	}

}(window, document, undefined, jQuery));

var news = News();
news.init();

