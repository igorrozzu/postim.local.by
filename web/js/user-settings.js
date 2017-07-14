var userSettings = (function (window, document, undefined, $) {

    return function () {
        const MAX_USER_PHOTO_SIZE = 10485760; //10mb
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
                        $('[name=' + prntId + ']').val(selectValue);

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
            },
            uploadUserPhotoHandler: function () {
                $('#user-photo').on('change', function (e) {
                    var file = e.target.files[0];
                    if(!that.validateUploadUserPhoto(file)) {
                        return false;
                    }

                    var form = new FormData();
                    form.append('user-photo', file);
                    $.ajax({
                        url: '/user/upload-photo',
                        type: "POST",
                        data: form,
                        cache: false,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: that.successUploadingUserPhotoHandler
                    });
                });
            },
            validateUploadUserPhoto: function (file) {
                var regexValidFormat = /(image\/jpeg)|(image\/png)|(image\/gif)/;
                if(!regexValidFormat.test(file.type)) {
                    $('#user-photo-uploading-error').text('Допустимы типы jpeg, png и gif');
                    return false;
                } else if(file.size > MAX_USER_PHOTO_SIZE) {
                    $('#user-photo-uploading-error').text('Размер фото не более ' + MAX_USER_PHOTO_SIZE + ' байт');
                    return false;
                }
                return true;
            },
            successUploadingUserPhotoHandler: function(response) {
                if(response.success){
                    $('.user-icon-profile img').attr('src', response.pathToPhoto);
                    $('.profile-icon-menu img').attr('src', response.pathToPhoto);
                    $('.user_icon img').attr('src', response.pathToPhoto);
                    $('#user-photo-uploading-error').text('');
                } else {
                    //TODO handle error from server
                }
            }
        };
        return that;
    }

}(window,document,undefined,jQuery));

var user_settings = userSettings();
user_settings.customScrollbarInit();
user_settings.selectBlockInit();
user_settings.uploadUserPhotoHandler();

