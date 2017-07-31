/**
 * Created by jrborisov on 8.7.17.
 */
var Main = (function (window, document, undefined,$) {

    return function () {


        var that = {

            init:function () {

                if (window.devicePixelRatio !== 1) { // Костыль для определения иных устройств, с коэффициентом отличным от 1
                    var dpt = window.devicePixelRatio;
                    var widthM = window.screen.width * dpt;
                    var widthH = window.screen.height * dpt;
                    document.write('<meta name="viewport" content="width=' + widthM+ ', height=' + widthH + '">');
                }

                $(document).ready(function () {

                    $(document).on('click','.btn-show-more',function () {

                        var params = {
                            selector:$(this).data('selector_replace'),
                            href:$(this).data('href')
                        };
                        showMore.render(params);
                    })
                });

                $(document).on('pjax:end', function(data, status, xhr, options) {
                    var target = $(data.target);

                    if (target.attr('id') == 'main-view-container') {
                        that.reloadViewPjaxEvents();
                    }

                });
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

            }
        }

        return that;
    }

}(window,document,undefined,jQuery));

var main = Main();
main.init();