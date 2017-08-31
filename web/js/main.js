/**
 * Created by jrborisov on 8.7.17.
 */
var Main = (function (window, document, undefined,$) {

    return function () {


        var that = {

            NewObj:function () {
               var newObj ={};
               return newObj;
            },

            init:function () {

                $(document).ready(function () {

                    $(document).on('click','.btn-show-more,.btn-load-more',function () {

                        var params = {
                            selector:$(this).data('selector_replace'),
                            href:$(this).data('href')
                        };

                        if(params.href!==undefined && params.selector !== undefined){
                            showMore.render(params);
                        }

                    });

                    $(document).off('click','.close-complaint-btn')
                        .on('click','.close-complaint-btn',function () {
                            main.closeFormComplaint();
                        })
                });

                $(document).on('pjax:end', function(data, status, xhr, options) {
                    var target = $(data.target);

                    if (target.attr('id') == 'main-view-container') {
                        that.reloadViewPjaxEvents();
                    }

                });
                that.shareSocialButtonsInit();


            },
            getDomainName:function () {
              return 'postim.local.by';
            },
            reloadViewPjaxEvents:function () {
                // init pjax ленты категорий
                $(document).off('click','#feed-category .block-sort a');
                $(document).pjax("#feed-category .block-sort a", "#feed-category", {"push":false,"replace":false,"timeout":60000,"scrollTo":false});

                // init pjax лент юзера
                $(document).off('click','.feeds-btn-bar a');
                $(document).pjax(".feeds-btn-bar a", {"push":false,"replace":false,"timeout":60000,"scrollTo":false,"container":"#feeds-of-user"});

                // init pjax настроек
                $(document).off('click','#pjax-container-settings a');
                $(document).off("submit", "#pjax-container-settings form");
                $(document).pjax("#pjax-container-settings a", {"push":false,"replace":false,"timeout":60000,"scrollTo":false,"container":"#pjax-container-settings"});
                $(document).on("submit", "#pjax-container-settings form", function (event) {$.pjax.submit(event, {"push":false,"replace":false,"timeout":60000,"scrollTo":false,"container":"#pjax-container-settings"});});

                $(document).off("click", ".menu-btns-card a");
                $(document).pjax(".menu-btns-card a", {"push":true,"replace":false,"timeout":60000,"scrollTo":false,"container":"#post-feeds"});

            },
            User:{
                is_guest:true
            },
            setSelectionRange:function (input, selectionStart, selectionEnd) {
                if (input.setSelectionRange) {
                    input.focus();
                    input.setSelectionRange(selectionStart, selectionEnd);
                }
                else if (input.createTextRange) {
                    var range = input.createTextRange();
                    range.collapse(true);
                    range.moveEnd('character', selectionEnd);
                    range.moveStart('character', selectionStart);
                    range.select();
                }
            },
            getFormComplaint:function () {
                if(main.getFormComplaint.cache==undefined)
                    main.getFormComplaint.cache = {};

                if(main.getFormComplaint.cache['Complaint']==undefined){
                    $.ajax({
                        url: '/site/get-form-complaint',
                        type: "GET",
                        async:false,
                        success: function (response) {
                            main.getFormComplaint.cache['Complaint']=response;
                        }
                    });
                }

                return  main.getFormComplaint.cache['Complaint'];
            },
            closeFormComplaint:function () {
                $('.container-popup-window.form-complaint').remove();
                $('.container-blackout-popup-window').hide();
            },
            showErrorAut:function (text) {
                $().toastmessage('showToast', {
                    text: text,
                    stayTime:5000,
                    type:'error'
                });

                $( ".sign_in_btn" ).trigger( "click" );
            },
            shareSocialButtonsInit:function () {
                $(document).on('click','.btn-social-share',function () {

                   var $block = $(this).parent().prev();
                   if($block.attr('is-open') === '1') {
                       $block.animate({right:'-160px',}, 500);
                       $block.attr('is-open', '0');
                   } else if($block.attr('is-open') === '0'){
                       $block.animate({right:'40px'}, 500);
                       $block.attr('is-open', '1');
                   }

                });
            },
            getLoadBlock:function () {
                return $('<div id="loader-box"><div class="loader"></div></div>');
            },

            UserGeolocation:function () {
                var __userCoords = {};
                var userGeolocation = {
                    isSupportedGeolocation:false,

                    init: function () {
                        $(document).ready(function () {
                            if (navigator.geolocation) {
                                userGeolocation.isSupportedGeolocation=true;
                            }
                        })

                    },
                    setGeolocation:function (name , value) {
                        if(value!==null && value != undefined){
                            $.cookie(name, JSON.stringify(value), {expires: 5, path: '/', domain: that.getDomainName(), secure: true});
                        }else {
                            $.cookie(name, null);
                        }

                    },
                    getGeolocation: function (name) {
                       return $.cookie(name) || null;
                    },
                    refreshGeolocation:function (position) {
                        if (position) {
                            __userCoords.lat = position.coords.latitude;
                            __userCoords.lon = position.coords.longitude;
                            userGeolocation.setGeolocation('geolocation',__userCoords)
                        }
                    },
                    requestGeolocation:function (cullBackF) {

                        navigator.geolocation.getCurrentPosition(function (position) {
                            if (position) {
                                 __userCoords = {};
                                __userCoords.lat = position.coords.latitude;
                                __userCoords.lon = position.coords.longitude;
                                userGeolocation.setGeolocation('geolocation', __userCoords);
                                cullBackF.call();

                            }
                        }, function (error) {
                            var text = 'Не удалось определить, где вы находитесь. Необходимо дать доступ к данным о вашем местоположении.';
                            if(error.code == 1){
                                text = 'У вас установлен запрет на определение местоположения, измените это в настройках браузера и повторите попытку.'
                            }

                            $().toastmessage('showToast', {
                                text: text,
                                stayTime:15000,
                                type:'error'
                            });
                        });

                        navigator.geolocation.watchPosition(userGeolocation.refreshGeolocation);
                    }

                };

                return userGeolocation;

            }
        };

        return that;
    }

}(window,document,undefined,jQuery));

var main = Main();
main.userGeolocation = main.UserGeolocation();
main.init();
main.userGeolocation.init();