/**
 * Created by jrborisov on 8.7.17.
 */
var PreviewPhoto = (function (window, document, undefined,$) {

    return function () {

        var that = {

            init:function () {
                $(document).ready(function () {
                    $(document).off('change','.photo-preview-add').on('change','.photo-preview-add',function (e) {
                        that.addPhotos.call(this, e);
                    });

                    $(document).off('click','.btn-close-photo-preview')
                        .on('click','.btn-close-photo-preview',function () {
                            that.deletePhoto($(this).parents('.item-photo-from-gallery'));
                        });

                    $(document).off('click','.btn-add-photo-preview')
                        .on('click','.btn-add-photo-preview',function () {
                            $('.photo-preview-add').trigger('click');
                        });
                });
            },

            addPhotos: function (e) {
                if (uploads.validatePhotos(e.target.files)) {
                    var form = new FormData();
                    $.each(e.target.files, function (key, value) {
                        form.append('photos[]', value);
                    });
                    uploads.uploadFiles('/post/upload-tmp-photo', form, that.renderPhotos);
                    $(this).val('');

                }else {
                    $().toastmessage('showToast', {
                        text     : 'Изображение должно быть в формате JPG, GIF или PNG.' +
                        ' Макс. размер файла: 15 МБ. Не более 10 файлов',
                        stayTime:  5000,
                        type     : 'error'
                    });
                }
            },

            renderPhotos: function (response) {
                var $tmp = $('<div id="" class="item-photo-from-gallery" style=""> ' +
                    '<div class="container-blackout"> ' +
                        '<div class="header-btns"> ' +
                        '   <span class="btn-item-photo btn-close-photo-preview"></span> ' +
                        '</div>  ' +
                    '</div> </div>');
                var $containerNewPhoto = $('<div></div>');

                if(response.success){

                    $tmp.css('background-image', 'url("/post_photo/tmp/' + response.data[0].link + '")');
                    $tmp.attr('id', that.getHashCode(response.data[0].link));
                    $containerNewPhoto.append($tmp.clone());

                    $('.block-gallery').html($containerNewPhoto.html());
                    $('#cover').val(response.data[0].link);
                    $('#cover').attr('value',response.data[0].link)


                }else {
                    $().toastmessage('showToast', {
                        text     : response.message,
                        stayTime:  5000,
                        type     : 'error'
                    });
                }
            },
            getHashCode : function(s){
                return s.split("").reduce(function(a,b){a=((a<<5)-a)+b.charCodeAt(0);return a&a},0);
            },

            deletePhoto: function ($elem) {
                $elem.remove();
            }


        }

        return that;
    }

}(window,document,undefined,jQuery));

var previewPhoto = PreviewPhoto();
previewPhoto.init();
