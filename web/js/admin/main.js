/**
 * Created by jrborisov on 8.7.17.
 */
var AdminMain = (function (window, document, undefined,$) {

    return function () {

        var that = {

            initEvents:function () {
                $(document).on('pjax:end', function(data, status, xhr, options) {
                    var target = $(data.target);

                    if (target.attr('id') == 'main-view-container') {
                        that.reloadViewPjaxEvents();
                    }

                });
            },

            reloadViewPjaxEvents:function () {

                $(document).off("submit", "#pjax-container-edit-page form");
                $(document).on("submit", "#pjax-container-edit-page form", function (event) {$.pjax.submit(event, {"push":false,"replace":false,"timeout":60000,"scrollTo":false,"container":"#pjax-container-edit-page"});});

                $(document).off("submit", "#pjax-container-add-news form");
                $(document).on("submit", "#pjax-container-add-news form", function (event) {$.pjax.submit(event, {"push":false,"replace":false,"timeout":60000,"scrollTo":false,"container":"#pjax-container-add-news"});});

                $(document).off('click','#pjax-container-add-biz a');
                $(document).pjax("#pjax-container-add-biz a", {"push":false,"replace":false,"timeout":60000,"scrollTo":false,"container":"#pjax-container-add-biz"});

                $(document).off("submit", "#pjax-container-add-biz form");
                $(document).on("submit", "#pjax-container-add-biz form", function (event) {$.pjax.submit(event, {"push":false,"replace":false,"timeout":60000,"scrollTo":false,"container":"#pjax-container-add-biz"});});


                $(document).off("submit", "#pjax-container-moderation form");
                $(document).off('click','#pjax-container-moderation a');
                $(document).pjax("#pjax-container-moderation a", {"push":false,"replace":false,"timeout":60000,"scrollTo":false,"container":"#pjax-container-moderation"});
                $(document).on("submit", "#pjax-container-moderation form", function (event) {$.pjax.submit(event, {"push":false,"replace":false,"timeout":60000,"scrollTo":false,"container":"#pjax-container-moderation"});});



            }

        }

        return that;
    }

}(window,document,undefined,jQuery));

var adminMain = AdminMain();
adminMain.initEvents();