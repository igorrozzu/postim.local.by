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

                $(document).off("submit", "#pjax-container-categories form");
                $(document).on("submit", "#pjax-container-categories form", function (event) {$.pjax.submit(event, {"push":false,"replace":false,"timeout":60000,"scrollTo":false,"container":"#pjax-container-categories"});});

                $(document).off("submit", "#pjax-container-moderation form");
                $(document).off('click','#pjax-container-moderation a');
                $(document).pjax("#pjax-container-moderation a", {"push":false,"replace":false,"timeout":60000,"scrollTo":false,"container":"#pjax-container-moderation"});
                $(document).on("submit", "#pjax-container-moderation form", function (event) {$.pjax.submit(event, {"push":false,"replace":false,"timeout":60000,"scrollTo":false,"container":"#pjax-container-moderation"});});



            },

            initFormCancels:function (id,callBack) {
                if(adminMain.initFormCancels.cache==undefined)
                    adminMain.initFormCancels.cache = {};

                if(adminMain.initFormCancels.cache['Cancels']==undefined){
                    $.ajax({
                        url: '/admin/moderation/get-form-cancels',
                        type: "GET",
                        async:false,
                        success: function (response) {
                            adminMain.initFormCancels.cache['Cancels']=response;
                        }
                    });
                }

                $('.container-blackout-popup-window').html(adminMain.initFormCancels.cache['Cancels']).show();
                $('.js-close-cancels').off('click').on('click',function () {
                    $('.container-blackout-popup-window').html('').hide();
                });

                $('.container-blackout-popup-window .form-cancels .js-cancels-btn').off('click').on('click',function () {
                    var message = $('.container-blackout-popup-window .form-cancels input[name="message"]').val();
                    $.ajax({
                        url: '/admin/moderation/cancels-reviews',
                        type: "POST",
                        dataType: "json",
                        data: {
                            id: id,
                            message: message
                        },
                        success: function (response) {
                            if (response.success){
                                $().toastmessage('showToast', {
                                    text: response.message,
                                    stayTime:5000,
                                    type: 'success'
                                });

                                if(callBack != undefined){
                                    callBack.call();
                                }

                                $('.container-blackout-popup-window').html('').hide();
                            } else {
                                $().toastmessage('showToast', {
                                    text: response.message,
                                    stayTime:8000,
                                    type: 'error'
                                });
                            }
                        }
                    });
                })

            },

        }

        return that;
    }

}(window,document,undefined,jQuery));

var adminMain = AdminMain();
adminMain.initEvents();