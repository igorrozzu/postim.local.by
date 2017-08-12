var Post = (function (window, document, undefined,$) {

    return function () {

        var that = {

            init:function () {

            },
            Info:function () {

                var $containerBlockInfo=null;

                var info={

                    init:function () {
                        $(document).ready(function () {
                            $(document).off('click','.btn-info-card').on('click','.btn-info-card',function (event) {
                                event.stopPropagation();
                                $container = $(this).parents('.info-row');
                                if($container.hasClass('active')){
                                    info.closeInfoBlock($container);
                                }else {
                                    info.openInfoBlock($container);
                                }

                            });

                            $(document).off('click','.info-row.show-open').on('click','.info-row.show-open',function () {
                                $container = $(this);
                                if($container.hasClass('active')){
                                    info.closeInfoBlock($container);
                                }else {
                                    info.openInfoBlock($container);
                                }

                            });

                            $(window).on('resize',$.debounce(250,info.calcShowOpenBtn))
                        });
                        $containerBlockInfo = $('.block-info-card');
                        info.calcShowOpenBtn();
                    },
                    openInfoBlock:function ($container) {
                        $container.addClass('active');
                        if($container.find('.phone-card').length){
                            $container.find('.phone-card').css('marginRight','0px').html('');
                            $container.find('.info-card-text').hide();

                        }
                    },
                    closeInfoBlock:function ($container) {
                        $container.removeClass('active');
                    },
                    calcShowOpenBtn:function () {
                        $container = $containerBlockInfo;
                        console.log($container);
                        $container.find('.info-row').each(function () {
                            var sumHeight=0;
                            $(this).find('.block-inside').children().each(function () {
                                sumHeight+= $(this).height();
                            });
                            console.log(sumHeight);
                            console.log($(this).height());
                            if(36<sumHeight){
                                $(this).addClass('show-open');
                            }else {
                                $(this).removeClass('show-open');
                            }
                        })
                    }

                }

                return info
            }

        };

        return that;
    }

}(window,document,undefined,jQuery));

var post = Post();
 post.info=post.Info();
post.init();
post.info.init();
