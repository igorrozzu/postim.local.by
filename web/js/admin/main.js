/**
 * Created by jrborisov on 8.7.17.
 */
var AdminMain = (function (window, document, undefined,$) {

    return function () {

        var that = {

            initEvents:function () {

                $(document).ready(function () {

                    $(document).off('click','.--delete')
                        .on('click','.--delete',function (e) {

                            if(!$(e.target).hasClass('confirm-href')) {
                                e.preventDefault();

                                $.confirm({
                                    title: 'Уведомление!',
                                    content: 'Вы точно хотите удалить?',
                                    buttons: {
                                        confirm:{
                                            text: 'Да',
                                            action:function () {
                                                $(e.target).addClass('confirm-href');
                                                $(e.target).trigger('click');
                                            },
                                        },
                                        cancel:{
                                            text: 'Отмена',
                                            action:function () {
                                                $(e.target).removeClass('confirm-href')
                                            }
                                        }
                                    }
                                });
                            }

                        })

                });
            },


            initFormCancels:function (callBack) {
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
                    callBack(message);
                })

            },

        }

        return that;
    }

}(window,document,undefined,jQuery));

var adminMain = AdminMain();
adminMain.initEvents();