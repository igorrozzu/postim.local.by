/**
 * Created by jrborisov on 8.7.17.
 */
var EnjoyhintTrunk = (function (window, document, undefined,$) {

    return function () {

        var that = {
            enjoyhint_instance: null,
            enjoyhint_script_steps: [],

            init: function () {

                if(!localStorage.getItem('EnjoyhintTrunkStartSteps')){
                    that.enjoyhint_instance = new EnjoyHint({});
                    that.startSteps();
                    localStorage.setItem('EnjoyhintTrunkStartSteps', 1);
                }



            },
            startSteps: function () {

                var script_array = [
                    {
                        selector:'.menu-btn',
                        event: 'click',
                        timeout: 3000,
                        description: 'Кликните по значку, чтобы попасть в меню',
                        skipButton: {text:'Пропустить'}
                    }

                ];

                that.enjoyhint_script_steps = script_array;

                that.run();

            },
            run: function () {
                that.enjoyhint_instance.set(that.enjoyhint_script_steps);
                that.enjoyhint_instance.run();
                that.enjoyhint_script_steps = [];
            }

        };

        return that;
    }

}(window,document,undefined,jQuery));

var enjoyhintTrunk = EnjoyhintTrunk();
$(document).ready(function () {
    setTimeout(function () {
        enjoyhintTrunk.init();
    },20000);
});

