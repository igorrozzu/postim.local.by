var userSettings = (function (window, document, undefined, $) {

    return function () {

        var that = {

            selectBlockInit:function () {
                $(document).ready(function () {
                    $(document).on('click','.select-value',function () {
                        $(this).next().click();
                    });
                    $(document).on('click', '.open-select-field', function () {
                        var id_block = $(this).data('open-id');
                        $(this).removeClass('open-select-field').addClass('close-select-field');
                        $('#' + id_block).addClass('open-select');
                    });

                    $(document).on('click', '.close-select-field', function () {
                        var id_block = $(this).data('open-id');
                        $(this).removeClass('close-select-field').addClass('open-select-field');
                        $('#' + id_block).removeClass('open-select');
                    });

                    $(document).on('click','.option-active .option-select-field',function () {
                        var prntId = $(this).parents('.container-scroll').attr('id');

                        var selectValue = $(this).data('value');
                        var selectLabel = $(this).html();

                        var preSelectValue = $('#' + prntId + '-value').data('value');
                        var preSelectLabel = $('#' + prntId + '-value').html();

                        $('#' + prntId + '-value').data('value', selectValue);
                        $('#' + prntId + '-value').html(selectLabel);

                        $(this).data('value', preSelectValue);
                        $(this).html(preSelectLabel);
                        if(preSelectValue==''){
                            $(this).remove()
                        }
                        $('#' + prntId + '-value').click();
                    })
                });
            },

            customScrollbarInit: function(){
                $(document).ready(function () {
                    $('.container-scroll-active').mCustomScrollbar({scrollInertia: 200});
                });
            }
        };
        return that;
    }

}(window,document,undefined,jQuery));

var user_settings = userSettings();
user_settings.customScrollbarInit();
user_settings.selectBlockInit();
