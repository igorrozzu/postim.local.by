/**
 * Created by jrborisov on 8.7.17.
 */
var Main = (function (window, document, undefined,$) {

    return function () {
        var that = {

            initEvents:function () {

                $(document).ready(function () {
                    $('.menu-content').mCustomScrollbar({scrollInertia: 50});
                });

                that.mainMenuInit();


            },

            mainMenuInit:function () {
                var mainMenu={isOpen:false}
                $(document).ready(function () {
                    $(document).on('click','.menu-btn,.close-main-menu',function () {
                        if(!mainMenu.isOpen){
                            mainMenu.isOpen=true;
                            $('.main-menu').animate({left:'0px',top:'0px'},200);
                        }else {
                            mainMenu.isOpen=false;
                            $('.main-menu').animate({left:'-300px',top:'0px'},200);
                        }
                    });

                    $(document).click(function (e) {
                        if ($(e.target).closest(".main-menu,.menu-btn").length) return;
                        mainMenu.isOpen=false;
                        $('.main-menu').animate({left:'-300px',top:'0px'},200);
                        e.stopPropagation();
                    });

                    $(document).on('click','.category-list-title',function () {
                        if($(this).find('span').hasClass('open-list-btn')){
                            closeOpenCategoryItem();
                            $(this).find('span').attr('class', 'close-list-btn');
                            $(this).parent().addClass('menu-category-open');
                            $(this).next(".menu-category-items").css('height', 'auto');
                        } else {
                            $(this).find('span').attr('class', 'open-list-btn');
                            $(this).parent().removeClass('menu-category-open');
                            $(this).next(".menu-category-items").css('height', '0');
                        }
                    });

                    function closeOpenCategoryItem(){
                        var item = $('.menu-category').find('.menu-category-open');
                        $(item).removeClass('menu-category-open');
                        $(item).find('.menu-category-items').css('height', '0');
                        $(item).find('.close-list-btn').attr('class', 'open-list-btn');
                    }
                })
            }
        }

        return that;
    }

}(window,document,undefined,jQuery));

var main = Main();
main.initEvents();