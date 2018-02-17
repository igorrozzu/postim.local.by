var Discount = (function (window, document, undefined, $) {

    return function () {

        var discount = {

            Adding: function (){

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
                                    var $form = $('#discount-form');
                                    var formData = $form.serialize();

                                    $.ajax({
                                        url: $form.attr('action'),
                                        type: 'POST',
                                        data: formData,
                                        success: function (response) {
                                            if (response.success) {
                                                location.href = response.redirectUrl;
                                            } else {
                                                $().toastmessage('showToast', {
                                                    text: response.message,
                                                    stayTime: 8000,
                                                    type: 'error'
                                                });
                                            }
                                        }
                                    })

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

                        $(document).off('input','#price,#price-with-discount')
                            .on('input','#price,#price-with-discount',function () {

                                var previewInputs = {
                                    price: $('#price'),
                                    economy: $('#economy'),
                                    priceWithDiscount: $('#price-with-discount'),
                                };

                                var price = parseFloat(previewInputs.price.val());
                                var priceWithDiscount = parseFloat(previewInputs.priceWithDiscount.val());

                                if (!isNaN(price) && !isNaN(priceWithDiscount) && price > 0
                                    && priceWithDiscount >= 0 && (economy = price - priceWithDiscount) > 0) {

                                    previewInputs.economy.val(economy.toFixed(2));

                                } else {
                                    previewInputs.economy.val('');
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

            Discount: function() {

                var scope = {
                    init: function () {
                        $(document).off('click','.order-discount.active')
                        .on('click','.order-discount.active', function () {
                            if (main.User.is_guest) {
                                main.showErrorAut('Незарегистрированные пользователи не могут брать промокоды');
                                return false;
                            }

                            $.ajax({
                                url: $(this).data('href'),
                                type: 'POST',
                                success: function (response) {
                                    if (response.success) {
                                        location.href = response.redirectUrl;
                                    } else {
                                        $().toastmessage('showToast', {
                                            text: response.message,
                                            stayTime: 8000,
                                            type: 'error'
                                        });
                                    }
                                }
                            })
                        });

                        $(document).on('click','.container-discount .btn-like', function () {
                            if (main.User.is_guest) {
                                main.showErrorAut('Неавторизованные пользователи не могут сохранять в Избранное понравившиеся');
                                return false;
                            }
                            var $this = $(this);
                            var itemId = $this.data('item-id');
                            var url = $this.data('favorites-state-url');
                            news.sendRequsetForStateItem($this, url, itemId, false);
                        });
                    },

                    setHeightToBlock: function () {
                        $(window).resize(function () {
                            var startWidth=900,
                                startHgt=440,
                                proportion=startWidth/startHgt;

                            var container = $('.container-discount-photos');
                            var width=$(container).width();
                            var height = width/proportion+'px';
                            $('.container-discount-photos').css({height:height});
                        });
                    },

                    monitorScroll: function () {
                        var rightBlock = $('.container-discount-info');
                        var mainBlock = $('#discount-index');
                        var headerHeight = 80;
                        var desctopMinWidth = 935;

                        var rightBlockOffsetTop = rightBlock.offset().top;
                        var rightBlockOffset = rightBlock.offset().top - headerHeight;

                        $(window).scroll(function() {

                            if ($(window).width() < desctopMinWidth) {
                                rightBlock.css({top: 0});
                                return;
                            }

                            var sctollTop = $(this).scrollTop();

                            if (sctollTop > rightBlockOffset) {
                                var top = mainBlock.outerHeight(true) <= sctollTop + rightBlock.outerHeight(true) + headerHeight ?
                                    mainBlock.outerHeight(true) - rightBlock.outerHeight(true) - rightBlockOffsetTop :
                                    sctollTop - rightBlockOffset;

                                rightBlock.css({
                                    top: top,
                                })

                            } else {
                                rightBlock.css({top: 0});
                            }
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

discount.mainPage = discount.Discount();
discount.mainPage.init();
