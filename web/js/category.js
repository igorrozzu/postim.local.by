/**
 * Created by jrborisov on 8.7.17.
 */
var Category = (function (window, document, undefined,$) {

    return function () {

        var that = {

            init:function () {
                $(document).ready(function () {

                    $(document).on('click','#feed-posts .btn-show-more,#feed-reviews .btn-show-more',function () {

                        var params = {
                            selector:$(this).data('selector_replace'),
                            href:$(this).data('href')
                        };
                        showMore.render(params);
                    })
                });
            }

        }

        return that;
    }

}(window,document,undefined,jQuery));

var category = Category();
category.init();

