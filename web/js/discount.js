var Discount = (function (window, document, undefined, $) {

    return function () {

        var discount = {

            Adding: function (){

                var previewInputs = {
                    price: $('#price'),
                    discount: $('#discount'),
                    economy: $('#economy'),
                    priceWithDiscount: $('#price-with-discount'),
                };

                var scope = {
                    init: function () {

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
                                $textarea.attr('name', 'discount[conditions][]');
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

                        $(document).off('click','#add-discount')
                            .on('click','#add-discount', function () {
                                if (editable.parserEditable()) {
                                    $('#discount-form').submit();
                                }
                            });

                        $(document).off('change','#discount-gallery')
                            .on('change','#discount-gallery',function (e) {
                                post_add.photos.addPhotos.call(this, e, '/discount/upload-tmp-photo');
                            });

                        $(document).off('change','#discount-gallery')
                            .on('change','#discount-gallery',function (e) {
                                post_add.photos.addPhotos.call(this, e, '/discount/upload-tmp-photo');
                            });

                        $(document).off('input','#price,#discount')
                            .on('input','#price,#discount',function () {
                                var price = parseFloat(previewInputs.price.val());
                                var discount = parseInt(previewInputs.discount.val());

                                if (!isNaN(price) && !isNaN(discount) && price > 0
                                    && discount >= 0 && discount <= 100) {

                                    var economy = (discount / 100) * price;
                                    previewInputs.economy.val(economy.toFixed(2));
                                    previewInputs.priceWithDiscount
                                        .val((price - economy).toFixed(2));
                                } else {
                                    previewInputs.economy.val('');
                                    previewInputs.priceWithDiscount.val('');
                                }
                            });
                    },
                };

                return scope;
            },

            Feed: function() {

                var scope = {
                    init: function () {
                        $(document).off('click','.block-discount').
                            on('click','.block-discount', function () {
                            $(this).parent().find('a.discount-link').trigger('click');
                        });

                        $(document).off('click','.card-block-discount a.discount-link').
                            on('click','.card-block-discount a.discount-link', function (e) {
                            if ($(e.target).hasClass('btn-like')) {
                                return false;
                            }
                        });

                        $(document).on('click','.card-block-discount .btn-like',function () {
                            if (main.User.is_guest) {
                                main.showErrorAut('Неавторизованные пользователи не могут сохранять в Избранное понравившиеся');
                                return false;
                            }
                            var $this = $(this);
                            var $block = $this.closest('.card-block-discount');
                            var itemId = $block.data('item-id');
                            var url = $block.closest('.cards-block-discount').data('favorites-state-url');
                            news.sendRequsetForStateItem($this, url, itemId);
                        })
                    }
                };

                return scope;
            },

            DiscountOrder: function() {

                var scope = {
                    init: function () {
                        $(document).off('click','.payment-methods .payment-block div').
                            on('click','.payment-methods .payment-block div', function () {
                            if ($(this).hasClass('disable')) {
                                return;
                            }

                            $('.payment-methods .payment-block div').removeClass('selected');
                            $(this).addClass('selected');

                            $('#discount-order-paymentType').val($(this).data('payment-type'));
                        });

                        $(document).off('click','.product-counter .add,.product-counter .remove').
                            on('click','.product-counter .add,.product-counter .remove', function () {
                            var scope = {
                                totalCostField: $('.product-total-cost #total-cost'),
                                counter: $('.counter'),
                                inputCounter: $('#discount-order-count'),
                                inputPaymentType: $('#discount-order-paymentType'),
                            };

                            var counterValue = parseInt(scope.counter.text());

                            if ($(this).hasClass('add')) {
                                ++counterValue;
                            } else if ($(this).hasClass('remove') && counterValue > 1) {
                                --counterValue;
                            }
                            scope.counter.text(counterValue);
                            scope.inputCounter.val(counterValue);

                            var totalCost = scope.totalCostField.data('start-value') * counterValue;
                            scope.totalCostField.text(totalCost.toFixed(2));
                        });

                        $(document).off('click','.btn-order-discount')
                            .on('click','.btn-order-discount', function () {
                                $('#discount-order-form').submit();
                            });
                    }
                };

                return scope;
            }
        };

        return discount;
    }

} (window, document, undefined, jQuery));

var discount = Discount();
discount.Adding().init();
discount.Feed().init();
discount.DiscountOrder().init();
