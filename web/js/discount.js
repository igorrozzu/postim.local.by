
var Discount = (function (window, document, undefined, $) {

    return function () {
        var customInput = `<div class="block-input-custom">
                    <input class="validator" data-error-parents="block-input-custom"
                           data-message="Неккоректные данные для условия"
                           placeholder="Укажите условие" name="condition[]"
                           data-regex="^\\S.{3,}">
                    <div class="close-input-custom"></div>
                </div>`;

        var discount = {

            initHandlers: function () {

                $(document).off('click', '.close-input-custom')
                    .on('click', '.close-input-custom', function () {
                        $(this).parents('.block-input-custom').remove();
                    });

                $(document).off('click', '#add-share-condition')
                    .on('click', '#add-share-condition', function () {
                        $(this).before(customInput);
                    });
            },
        };

        return discount;
    }

} (window, document, undefined, jQuery));

var discount = Discount();
discount.initHandlers();


