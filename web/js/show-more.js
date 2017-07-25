/**
 * Created by jrborisov on 8.7.17.
 */
var ShowMore = (function (window, document, undefined,$) {

    return function () {

        var that = {

            render:function (p) {
                var selectorContainerReplace = p['selector'];
                var href = p['href'];

                $.ajax({
                    url: href,
                    type: "GET",
                    async:false,
                    success: function (response) {
                        $(selectorContainerReplace).replaceWith(response);
                    }
                });

            }

        }

        return that;
    }

}(window,document,undefined,jQuery));

var showMore = ShowMore();
