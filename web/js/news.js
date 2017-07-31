/**
 * Created by jrborisov on 8.7.17.
 */
var News = (function (window, document, undefined,$) {

    return function () {

        var that = {

            init: function () {
                $(document).ready(function () {


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

