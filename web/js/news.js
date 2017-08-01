/**
 * Created by jrborisov on 8.7.17.
 */
var News = (function (window, document, undefined,$) {

    return function () {
        var methods = {
            defineUrlByItemType: function (type) {
                switch (type) {
                    case 'post' : return '/post/favorite-state'; break;
                    case 'news' : return '/news/favorite-state'; break;
                }
            }
        };
        var that = {

            init: function () {
                $(document).ready(function () {
                    that.addToFavorite();

                });
            },
            addToFavorite: function () {
                $(document).on('click', '.bookmarks-btn-active,.bookmarks-btn', function () {
                    var block = $(this).closest('.card-block');
                    var item_id = block.data('item-id');
                    var url = methods.defineUrlByItemType(block.data('type'));
                    var action = null;
                    if($(this).hasClass('bookmarks-btn')) {
                        $(this).removeClass('bookmarks-btn').addClass('bookmarks-btn-active');
                        action = 'add';
                    } else {
                        $(this).removeClass('bookmarks-btn-active').addClass('bookmarks-btn');
                        action = 'remove';
                    }
                    that.sendRequsetForStateItem(url, item_id, action);
                    return false;
                });
            },

            sendRequsetForStateItem: function (url, item_id, action) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {action: action, itemId: item_id}
                });
            },

            Comments: function () {
                var comments = {
                    init: function () {
                        $(document).ready(function () {


                        });
                    }
                };

                return comments;
            }

        }

        return that;
    }

}(window,document,undefined,jQuery));

var news = News();
var newsComments = news.Comments();
news.init();
newsComments.init();

