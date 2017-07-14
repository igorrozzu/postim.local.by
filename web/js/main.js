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

                /*var map;
                ymaps.ready(function(){
                    map = new ymaps.Map("map_block", {
                        center: [53.52, 28.20],
                        zoom: 10
                    });
                });*/

            }
        }

        return that;
    }

}(window,document,undefined,jQuery));

var main = Main();
main.init();