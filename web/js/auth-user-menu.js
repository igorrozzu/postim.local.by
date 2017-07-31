var authUserMenu = (function (window, document, undefined,$) {

    return function () {
        var methods = {
            sendRequestOfGettingNotification: function (time) {
                var container = $('.replace-notif-block');
                var url  = container.attr('href');
                $.ajax(url, {
                   type: 'GET',
                   data: 'time=' + time,
                   success: function (response) {
                       container.replaceWith(response.rendering);
                   }
                });
            },
            sendRequestOfGettingCountNotification: function () {
                $.ajax('/notification/get-count-notifications', {
                    type: 'POST',
                    success: function (response) {
                        methods.changeNoticeBlock(response);
                    }
                });
            },
            sendRequestOfMarkNotifAsRead: function () {
                $.ajax('/notification/mark-as-read', {
                    type: 'POST',
                    success: function (response) {
                        if (response < 0) {
                            console.log('Error marking of notification');
                        } else {
                            that.updatingNotificationOn();
                        }
                    }
                });
            },
            changeNoticeBlock: function (noticeCount) {
                var container = $('.btn-notice');
                var countNoticeBlock = container.find('.count-notice');
                var currentNotifCount = parseInt(countNoticeBlock.text());
                if(noticeCount > 0) {
                    if(noticeCount !== currentNotifCount) {
                        if (countNoticeBlock.length) {
                            countNoticeBlock.text(noticeCount);
                        } else {
                            container.html('<span class="count-notice">' + noticeCount + '</span>')
                        }
                        container.removeClass('btn-notice-unactive').addClass('btn-notice-active');
                    }
                } else {
                    if(countNoticeBlock.length) {
                        countNoticeBlock.remove();
                        container.removeClass('btn-notice-active').addClass('btn-notice-unactive');
                    }
                }
            },
            resetNotifMenu: function () {
                methods.sendRequestOfMarkNotifAsRead();
                $('.notif-menu .notif-content').replaceWith(
                '<div class="notif-content">' +
                    '<div class="replace-notif-block" href="/notification/index">' +
                    '<div id="loader-box">' +
                    '<div class="loader"></div>' +
                    '</div></div></div>'
                );
                that.customScrollbarInit();
            },
        };

        var that = {
            intervalId: null,
            intervalTime: 1000 * 10,
            userProfileMenuInit:function () {
                var rightMenu={isOpen:false};
                $(document).ready(function () {
                    $(document).on('click','.profile-icon-menu,.right-menu-profile .btn-close',function () {
                        if(!rightMenu.isOpen){
                            rightMenu.isOpen=true;
                            $('.right-menu-profile').animate({right:'0px',top:'0px'},200);
                        }else {
                            rightMenu.isOpen=false;
                            $('.right-menu-profile').animate({right:'-300px',top:'0px'},200);
                        }
                    });

                    $(document).click(function (e) {
                        if ($(e.target).closest(".right-menu-profile,.profile-icon-menu,.right-menu-profile .btn-close").length) return;
                        rightMenu.isOpen=false;
                        $('.right-menu-profile').animate({right:'-300px',top:'0px'},200);
                        e.stopPropagation();
                    })
                    $(document).on('click','.right-menu-profile .container-item-menu a',function () {
                        menu_control.fireMethodClose();
                        $(this).parents('.container-item-menu').addClass('selected');
                        setTimeout(function () {
                            $( ".right-menu-profile .btn-close" ).trigger( "click" );
                        },100)
                    })

                })


            },
            unselectProfileMenu:function () {
                $('.right-menu-profile .container-item-menu').removeClass('selected');
            },

            notificationMenuInit:function () {
                var notifMenu={
                    isOpen:false, width: '-484px', openTime: 200, pointTime: null
                };
                $(document).ready(function () {
                    $(document).on('click','.btn-notice,.right-arrow',function () {
                        if(!notifMenu.isOpen){
                            notifMenu.isOpen=true;
                            notifMenu.pointTime = Math.floor(Date.now() / 1000);
                            $('.notif-menu').animate({right:'0px', top:'0px'}, notifMenu.openTime);
                        }else {
                            notifMenu.isOpen=false;
                            $('.notif-menu').animate({right:notifMenu.width, top:'0px'}, {
                                duration: notifMenu.openTime,
                                complete: methods.resetNotifMenu
                            });
                        }
                    });

                    $(document).click(function (e) {
                        if ($(e.target).closest(".notif-menu,.btn-notice").length) return;
                        if(notifMenu.isOpen) {
                            notifMenu.isOpen = false;
                            $('.notif-menu').animate({right: notifMenu.width, top: '0px'}, {
                                duration: notifMenu.openTime,
                                complete: methods.resetNotifMenu
                            });
                            e.stopPropagation();
                        }
                    });

                    $(document).on('click', '.btn-notice,.replace-notif-block .bottom-btn', function () {
                        methods.sendRequestOfGettingNotification(notifMenu.pointTime);
                    });

                    $(document).on('click', '.btn-notice', function () {
                        that.updatingNotificationOff();
                        methods.changeNoticeBlock(0);
                    });

                })
            },
            customScrollbarInit: function(){
                $(document).ready(function () {
                    $('.notif-content,.container-body-right-menu').mCustomScrollbar({scrollInertia: 300});
                });
            },

            closeNotificationFormHandler: function () {
                $(document).on('click', '.close-notif-message', function () {
                    $('.container-blackout-popup-window').hide();
                });
            },
            updatingNotificationOn: function() {
                //this.intervalId = setInterval(methods.sendRequestOfGettingCountNotification, this.intervalTime);
            },
            updatingNotificationOff: function() {
                clearInterval(this.intervalId);
            },
            init: function () {
                that.customScrollbarInit();
                that.userProfileMenuInit();
                that.notificationMenuInit();
                that.closeNotificationFormHandler();
                that.updatingNotificationOn();
            }
        };
        return that;
    }

}(window,document,undefined,jQuery));

var authMenu = authUserMenu();
menu_control.addMethodMenuClose(authMenu.unselectProfileMenu);
authMenu.init();