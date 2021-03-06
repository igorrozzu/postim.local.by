var Menu = (function (window, document, undefined,$) {

    return function () {

        var __mainMenu={isOpen:false};

        var that = {

            catalogMenuInit:function () {

                $(document).ready(function () {

                    $(document).off('click', '.catalog-category').on('click', '.catalog-category', function (event) {
                        var $target = $(event.target);
                        if ($target.hasClass('catalog-list-item')) {
                            event.stopPropagation();
                            return false;
                        }

                        if ($(this).data('open') == false) {
                            closeOpenCategoryCatalogItem();
                            $(this).find('.catalog-title .catalog-title-name').addClass('open-catalog');
                            $(this).find('.catalog-title .catalog-title-btn').addClass('btn-top');
                            $(this).find('.catalog-title .catalog-title-btn').removeClass('btn-down');
                            $(this).find('.catalog-list').height('auto');
                            $(this).data('open', true);
                        } else {
                            $(this).find('.catalog-title .catalog-title-name').removeClass('open-catalog');
                            $(this).find('.catalog-title .catalog-title-btn').addClass('btn-down');
                            $(this).find('.catalog-title .catalog-title-btn').removeClass('btn-top');
                            $(this).find('.catalog-list').height('0');
                            $(this).data('open', false);
                        }
                    })

                    $(document).on('click','.catalog-list-item',function () {
                        var under_category = $(this).data('under_category_name')||'NuN';
                        var category = $(this).parents('.catalog-category').data('category_name');
                        that.openCategoryInLeftMenu(category,under_category);

                    })
                });

                function closeOpenCategoryCatalogItem() {
                    $('.catalog-title .catalog-title-name').removeClass('open-catalog');
                    $('.catalog-title .catalog-title-btn').addClass('btn-down');
                    $('.catalog-title .catalog-title-btn').removeClass('btn-top');
                    $('.catalog-list').height('0');
                    $('.catalog-category').data('open', false);
                }

            },

            leftMenuInit:function () {
                $(document).ready(function () {

                    main.initCustomScrollBar($('.menu-content'),{scrollInertia: 50});
                    $('.left-menu .menu-content').mCustomScrollbar('destroy');

                    var $mainMenu=$('.main-menu');

                    $(document).on('click','.menu-btn,.close-main-menu',function () {
                        $mainMenu=$('.main-menu');
                        if(!__mainMenu.isOpen){
                            __mainMenu.isOpen=true;
                            $mainMenu.animate({left:'0px',top:'0px'},200);
                        }else {
                            __mainMenu.isOpen=false;
                            $mainMenu.animate({left:'-300px',top:'0px'},200);
                        }
                    });

                    $(document).click(function (e) {
                        if ($(e.target).closest(".main-menu,.menu-btn").length) return;
                        __mainMenu.isOpen=false;
                        $mainMenu.animate({left:'-300px',top:'0px'},200);
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

					$(document).on('click', '.news-list-title', function (e) {
					    if(!$(e.target).is('a')){
							$(this).find('a').trigger('click');
                        }
						__mainMenu.isOpen=false;
						$mainMenu.animate({left:'-300px',top:'0px'},200);
					});


                    function closeOpenCategoryItem(){
                        var $item = $('.menu-category').find('.menu-category-open');
                        $item.removeClass('menu-category-open');
                        $item.find('.menu-category-items').css('height', '0');
                        $item.find('.close-list-btn').attr('class', 'open-list-btn');
                    }

                    $(document).on('click','.menu-category-items .menu-category-item',function () {
                        $(this).parents('.main-menu').find('.selected').removeClass('selected');
                        $(this).addClass('selected');

                    })

                    $(document).on('click','.menu-category-item a',function () {
                        setTimeout(function () {
                            $( ".close-main-menu" ).trigger( "click" );
                        },100)
                    });
                })

            },
			openPageInLeftMenu:function ($elem) {
				menu_control.fireMethodClose();
				$elem.addClass('menu-category-open');
			},
            openCategoryInLeftMenu:function (category,under_category) {
                menu_control.fireMethodClose();

                var $elem = $(".category-list-title[data-category_name='"+category+"']");
                $elem.find('span').attr('class', 'close-list-btn');
                $elem.parent().addClass('menu-category-open');
                $elem.next(".menu-category-items").css('height', 'auto');

                if(under_category!='NuN'){
                    $elem.parent().find(".menu-category-item[data-under_category_name='"+under_category+"']").addClass('selected');
                }else {
                    $elem.parent().find(".menu-category-item:first-child").addClass('selected');
                }
            },
            unselectAllCategory:function () {
                var $item = $('.menu-category').find('.menu-category-open');
                $item.removeClass('menu-category-open');
                $item.find('.menu-category-items').css('height', '0');
                $item.find('.close-list-btn').attr('class', 'open-list-btn');
                $item.find('.selected').removeClass('selected');
            },

            Control:function () {
              var __repository_methods_close_menu={};

              var control = {

                  addMethodMenuClose: function (method) {
                      if (method.name && !(method.name in __repository_methods_close_menu)) {
                          __repository_methods_close_menu[method.name] = method;
                      }

                  },
                  fireMethodClose:function () {
                      for (var method_name in __repository_methods_close_menu){
                          __repository_methods_close_menu[method_name].call()
                      }
                  }
              };

              return control;

            }
        }

        return that;
    }

}(window,document,undefined,jQuery));

var menu = Menu();
var menu_control =menu.Control();
menu.leftMenuInit();
menu.catalogMenuInit();
menu_control.addMethodMenuClose(menu.unselectAllCategory);