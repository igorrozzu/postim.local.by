(function($) {

    var selector = '.lazy';

    $(document).on('scroll',function () {
        $(this).trigger('lazyLoad:check');
    });

    $(document).on('lazyLoad:check',function () {

        if(!$(selector).length) return false; // element not found

        var docViewTop = $(window).scrollTop();
        var docViewBottom = docViewTop + $(window).height();

        $(selector).each(function () {
            var elemTop = $(this).offset().top;
            var elemBottom = elemTop + $(this).height();

            if((docViewTop < elemTop) && (docViewBottom > elemBottom+-400)){
                $(this).trigger('lazyLoad:isVisible');
            }

        });

    });

    $(document).on('lazyLoad:isVisible','.lazy',function (e) {
        var $elem = $(e.target);
        var src = $elem.data('src') || undefined;

        if(src != undefined){
            $(new Image()).attr('src', src).load(function() {
                $elem.css({backgroundImage:'url(\''+src+'\')'});
            });
        }

        $elem.removeClass('lazy');
    });

    $(document).on('pjax:end', function(data, status, xhr, options) {
        $(document).trigger('lazyLoad:check');
    });

    $(document).trigger('lazyLoad:check');


})(jQuery);