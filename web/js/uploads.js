var Uploads = (function (window, document, undefined,$) {

    return function () {

        var that = {
            photo: {
                validFormats: /(image\/jpeg)|(image\/png)|(image\/gif)/,
                maxPhotoSize:  15728640, //15 мб
                maxPhotoCount: 10,
            },
            uploadFiles: function (action, data, handler) {
                $.ajax({
                    url: action,
                    type: "POST",
                    data: data,
                    cache: false,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: handler
                });
                return true
            },

            uploadByURL: function (action, url, handler) {
                $.ajax({
                    url: action,
                    type: "POST",
                    data: {url:url},
                    dataType: 'json',
                    success: handler
                });
            },

            validatePhotos: function (files) {
                if (files.length > that.photo.maxPhotoCount) {
                    return false;
                }
                for (var i in files) {
                    if(!that.photo.validFormats.test(files[i].type) ||
                        files[i].size > that.photo.maxPhotoSize) {
                        return false;
                    }
                    return true;
                }
            },

        };

        return that;
    }

}(window,document,undefined,jQuery));

var uploads = Uploads();

