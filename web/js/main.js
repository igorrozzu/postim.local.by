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
                        });
					$(document).off('click', '.btn_add_place,.btn_br')
						.on('click', '.btn_add_place,.btn_br', function (e) {
							if (that.User.is_guest) {
								that.showErrorAut('Незарегистрированные пользователи не могут добавить место');
								e.preventDefault();

								return false;
							}
						});

                    $(document).off('click','.add-place-href')
                        .on('click','.add-place-href',function () {
                            $('.btn_add_place').trigger('click');
                        })
                });

                $(document).on('pjax:end', function(data, status, xhr, options) {
                    var target = $(data.target);

                    if (target.attr('id') == 'main-view-container') {
                        that.reloadViewPjaxEvents();
                    }

                });
                that.shareSocialButtonsInit();

                that.showWindowsPush();

            },
            getDomainName:function () {
              return window.location.host;
            },
			getHashCode:function (s) {
				return s.split("").reduce(function(a,b){a=((a<<5)-a)+b.charCodeAt(0);return a&a},0);
			},
            reloadViewPjaxEvents:function () {
                // init pjax ленты категорий
                $(document).off('click','#feed-category .block-sort a');
                $(document).pjax("#feed-category .block-sort a", "#feed-category", {"push":false,"replace":false,"timeout":60000,"scrollTo":false});

                // init pjax лент юзера
                $(document).off('click','#feeds-of-user .feeds-btn-bar a');
                $(document).pjax("#feeds-of-user .feeds-btn-bar a", {"push":false,"replace":false,"timeout":60000,"scrollTo":false,"container":"#feeds-of-user"});

                // init pjax настроек
                $(document).off('click','#pjax-container-settings a');
                $(document).off("submit", "#pjax-container-settings form");
                $(document).pjax("#pjax-container-settings a", {"push":false,"replace":false,"timeout":60000,"scrollTo":false,"container":"#pjax-container-settings"});
                $(document).on("submit", "#pjax-container-settings form", function (event) {$.pjax.submit(event, {"push":false,"replace":false,"timeout":60000,"scrollTo":false,"container":"#pjax-container-settings"});});

                $(document).off("submit", "#pjax-container-feedback form");
                $(document).on("submit", "#pjax-container-feedback form", function (event) {$.pjax.submit(event, {"push":false,"replace":false,"timeout":60000,"scrollTo":false,"container":"#pjax-container-feedback"});});

                $(document).off("click", "#post-feeds .menu-btns-card a");
                $(document).pjax("#post-feeds .menu-btns-card a", {"push":true,"replace":false,"timeout":60000,"scrollTo":false,"container":"#post-feeds"});

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

            initFormComplaint:function (id,type,callBack) {
                if(main.initFormComplaint.cache==undefined)
                    main.initFormComplaint.cache = {};

                if(main.initFormComplaint.cache['Complaint']==undefined){
                    $.ajax({
                        url: '/site/get-form-complaint',
                        type: "GET",
                        async:false,
                        success: function (response) {
                            main.initFormComplaint.cache['Complaint']=response;
                        }
                    });
                }

                $('.container-blackout-popup-window').html(main.initFormComplaint.cache['Complaint']).show();
                $('.container-blackout-popup-window .form-complaint .complain-btn').on('click',function () {
                    var message = $('.container-blackout-popup-window .form-complaint input[name="complain"]').val();
                    $.ajax({
                        url: '/site/add-complain',
                        type: "POST",
                        dataType: "json",
                        data: {
                            id: id,
                            message: message,
                            type:type
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

                                main.closeFormComplaint();
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

            getFormEntities:function (url) {
                var form = null;
                $.ajax({
                    url: url,
                    type: "GET",
                    async:false,
                    success: function (response) {
                        form = response;
                    }
                });

                return  form;
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
            initMainLoadBlock:function () {
                that.stopMainLoadBlock();
                var $html = $('<div class="main-preload"><div id="loader-box2"><div class="loader"></div></div></div>');
                $('body').append($html);
            },

            stopMainLoadBlock: function(){
                $('.main-preload').remove();
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

            },
            
            isMobile:function () {
                var md=new MobileDetect(window.navigator.userAgent); //get device type
                return md.mobile();
            },
            initCustomScrollBar:function ($elem,settings) {

                if(!main.isMobile()){
                    $elem.mCustomScrollbar(settings);
                }else {
                    $elem.removeClass('h_scroll').addClass('h_scroll');
                }

            },

            showWindowsPush:function () {

                var date = new Date();
                var currentTime = date.getTime();
                var expires = currentTime + (3600 * 60 * 1000);

                var params = null;

                if (params = localStorage.getItem('showWindowsPush')) {
                    params = JSON.parse(params);
                } else {
                    params = {value: false, expires: currentTime};
                }

                if (!params.value && currentTime >= params.expires) {

                    var html = '<div class="push-message-request"><div class="push-message-text">Вы не против подписаться на<br><span>важные </span>новости от Postim.by</div><div class="push-message-btn sp_notify_prompt" onclick="oSpP.startSubscription();">Нет, не против</div></div>';

                    setTimeout(function () {
                        $().toastmessage('showToast', {
                            text: html,
                            stayTime: 1500000,
                            type: 'success'
                        });

                        params.expires = expires;
                        params.value = true;

                        localStorage.setItem('showWindowsPush', JSON.stringify(params));
                        
                        $('.push-message-btn').click(function () {

                            $(this).parents('.toast-item-wrapper')
                                .find('.toast-item-close').click();
                        })

                    }, 3000);

                }

            }
        };

        return that;
    }

}(window,document,undefined,jQuery));

var main = Main();
main.userGeolocation = main.UserGeolocation();
main.init();
main.userGeolocation.init();