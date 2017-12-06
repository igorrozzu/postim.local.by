
var Discount = (function (window, document, undefined, $) {

    return function () {

        var discount = {

            initHandlers: function () {

                $(document).off('click', '.close-input-custom')
                    .on('click', '.close-input-custom', function () {

                        var $field = $(this).closest('.option-select-field');

                        if ($field.hasClass('another-condition')) {
                            $field.remove();
                        } else {
                            var $textarea = $(this).prev();

                            $field.children().addClass('hidden');
                            $textarea.attr('readonly', '');
                            $textarea.removeAttr('name');
                            $textarea.val($textarea.data('preview-text'));

                            if ($field.children().hasClass('error')) {
                                $field.children().removeClass('error')
                            }
                            $textarea.css('height', 'auto');

                            $('#select-condition').prepend($field);
                            $textarea.trigger('input');
                        }
                    });

                $(document).off('click', '#select-condition .option-select-field')
                    .on('click', '#select-condition .option-select-field', function () {
                        var $this;

                        if ($(this).hasClass('another-condition')) {
                            $this = $(this).clone();
                        } else {
                            $this = $(this);
                        }

                        var $textarea = $this.find('textarea');
                        var $btnAddCondition = $('#add-share-condition');

                        $this.children().removeClass('hidden');
                        $textarea.attr('name', 'condition[]');
                        $textarea.removeAttr('readonly');
                        $textarea.val($textarea.data('preview-text') +
                            $textarea.data('continue-text'));

                        $('#select-condition-value').trigger('click');
                        $textarea.trigger('input');
                        $btnAddCondition.before($this);

                        if ($this.hasClass('another-condition')) {
                            $textarea.autosize();
                        }
                    });
            },
        };

        return discount;
    }

} (window, document, undefined, jQuery));

var discount = Discount();
discount.initHandlers();


