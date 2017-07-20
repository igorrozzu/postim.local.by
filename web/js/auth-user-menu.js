var authUserMenu = (function (window, document, undefined,$) {

    return function () {

        var that = {

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
                })


            },

            notificationMenuInit:function () {
                var notifMenu={
                    isOpen:false, width: '-484px', openTime: 200
                };
                $(document).ready(function () {
                    $(document).on('click','.btn-notice,.right-arrow',function () {
                        if(!notifMenu.isOpen){
                            notifMenu.isOpen=true;
                            $('.notif-menu').animate({right:'0px', top:'0px'}, notifMenu.openTime);
                        }else {
                            notifMenu.isOpen=false;
                            $('.notif-menu').animate({right:notifMenu.width, top:'0px'}, notifMenu.openTime);
                        }
                    });
                    $(document).click(function (e) {
                        if ($(e.target).closest(".notif-menu,.btn-notice").length) return;
                        notifMenu.isOpen=false;
                        $('.notif-menu').animate({right:notifMenu.width, top:'0px'}, notifMenu.openTime);
                        e.stopPropagation();
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

            init: function () {
                that.customScrollbarInit();
                that.userProfileMenuInit();
                that.notificationMenuInit();
                that.closeNotificationFormHandler();
            }
        };
        return that;
    }

}(window,document,undefined,jQuery));

var authMenu = authUserMenu();
authMenu.init();