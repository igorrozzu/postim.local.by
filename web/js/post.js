var Post = (function (window, document, undefined,$) {

    return function () {

        var that = {

            init:function () {
                that.transitionToPostHandler();
            },
            transitionToPostHandler: function () {
                $(document).off('click','.card-block').on('click','.card-block', function (e) {
                    if(!$(e.target).is('a')) {
                        $(this).find('.main-pjax a').trigger('click');
                    }

                });
            },

            Info:function () {

                var $containerBlockInfo=null;

                var info={

                    init:function () {
                        $(document).ready(function () {
                            $(document).off('click','.btn-info-card').on('click','.btn-info-card',function (event) {
                                event.stopPropagation();
                                $container = $(this).parents('.info-row');
                                if($container.hasClass('active')){
                                    info.closeInfoBlock($container);
                                }else {
                                    info.openInfoBlock($container);
                                }

                            });

                            $(document).off('click','.info-row.show-open').on('click','.info-row.show-open',function () {
                                $container = $(this);
                                if($container.hasClass('active')){
                                    info.closeInfoBlock($container);
                                }else {
                                    info.openInfoBlock($container);
                                }

                            });

                            $(window).on('resize',$.debounce(250,info.calcShowOpenBtn))
                        });
                        $containerBlockInfo = $('.block-info-card');
                        info.calcShowOpenBtn();
                    },
                    openInfoBlock:function ($container) {
                        $container.addClass('active');
                        if($container.find('.phone-card').length){
                            $container.find('.phone-card').css('marginRight','0px').html('');
                            $container.find('.info-card-text').hide();

                        }
                    },
                    closeInfoBlock:function ($container) {
                        $container.removeClass('active');
                    },
                    calcShowOpenBtn:function () {
                        $container = $containerBlockInfo;

                        $container.find('.info-row').each(function () {
                            var sumHeight=0;
                            $(this).find('.block-inside').children().each(function () {
                                sumHeight+= $(this).height();
                            });
                            if(36<sumHeight){
                                $(this).addClass('show-open');
                            }else {
                                $(this).removeClass('show-open');
                            }
                        })
                    }

                }

                return info
            },
            Photos: function () {
                var _container = {
                    url: false,
                    currentItem: -1,
                    loadTime: null,
                    postId: null,
                    data: [],
                    sources: [],
                    imgSize: {height: null, width: null},
                    state: {
                        $photoBox: null,
                        $sourceBox: null,
                        $leftSliderButton: null,
                        $rightSliderButton: null,
                        left: true,
                        right: true
                    },

                    next: function () {
                        if (_container.currentItem === 0) {
                            _container.state.$photoBox.removeClass('prev-btn-hide');
                            _container.state.left = true;
                        }
                        _container.currentItem++;
                        if (_container.data[_container.currentItem + 1] === undefined) {
                            _container.loadToContainer();
                        }
                        if (_container.data[_container.currentItem + 1] === undefined && _container.url === null) {
                            _container.state.right = false;
                            _container.state.$photoBox.addClass('next-btn-hide');
                        }
                        return this.data[this.currentItem];

                    },
                    prev: function () {
                        _container.currentItem--;
                        if (_container.currentItem === 0) {
                            _container.state.$photoBox.addClass('prev-btn-hide');
                            _container.state.left = false;
                        }
                        if (_container.data[_container.currentItem + 2] === undefined) {
                            _container.state.$photoBox.removeClass('next-btn-hide');
                            _container.state.right = true;
                        }
                        return this.data[this.currentItem];
                    },
                    current: function () {
                        that.methods.initContainer();

                        if(_container.data[_container.currentItem] === undefined) {
                            _container.loadToContainer();
                        }
                        if(_container.currentItem === 0) {
                            _container.state.$photoBox.addClass('prev-btn-hide');
                            _container.state.left = false;
                        }
                        if(_container.data[_container.currentItem + 1] === undefined && _container.url === null) {
                            _container.state.$photoBox.addClass('next-btn-hide');
                            _container.state.right = false;
                        }
                        return this.data[this.currentItem];

                    },

                    loadToContainer: undefined,
                    loadPhotosFromPage: function () {
                        if(_container.data.length === 0) {
                            $('.block-photos-owner .container-photo').each(function () {
                                var img = $(this).css('background-image');
                                var match = img.match(/post_photo\/\d+\/(.+)"\)$/);
                                _container.data.push(match[1]);
                                _container.sources.push($(this).data('source'));
                            })
                        }
                    },
                    loadPhotosFromServer: function () {
                        if(_container.url !== null) {
                            $.ajax({
                                url: _container.url,
                                dataType: 'json',
                                async: false,
                                success: function (response) {
                                    _container.url = response.url;
                                    for (var i in response.data) {
                                        _container.data.push(response.data[i].link);
                                        _container.sources.push(response.data[i].source);
                                    }
                                }
                            });
                        }
                    },
                    resetState: function () {
                        _container.state.left = true;
                        _container.state.right = true;
                        _container.state.$photoBox.removeClass('prev-btn-hide');
                        _container.state.$photoBox.removeClass('next-btn-hide');
                        _container.state.$photoBox = null;
                    },
                };


                var that = {
                    setLoadTime: function (loadTime) {
                        _container.loadTime = loadTime;
                    },
                    setPostId: function (postId) {

                        _container.data = [];
                        _container.sources = [];
                        _container.url = false;
                        _container.postId = postId;

                    },

                    methods: {
                        resizePopupPhoto: function () {
                            setTimeout(function () {

                                if ($('.photo-popup').css('display') !== 'none') {
                                    that.methods.calcSizePhoto();
                                    var wrapHeight = $(".photo-wrap").height();
                                    var wrapWidth = $(".photo-wrap").width();
                                    var $img = $(".photo-wrap img");
                                    $img.css({
                                        maxWidth: wrapWidth + 'px',
                                        maxHeight: wrapHeight + 'px'
                                    });

                                    $('.pre-popup-photo, .next-popup-photo').css('height', $img.height());
                                }
                            }, 10)
                        },
                        calcSizePhoto: function () {
                            var $img = $(".photo-wrap img");
                            _container.imgSize.height = $img.height();
                            _container.imgSize.width = $img.width();
                        },
                        showPopupPhoto: function (imgUrl) {
                            $('.container-blackout-photo-popup').css('display', 'block');
                            $('.photo-popup').css('display', 'flex');
                            $('body').css('overflow', 'hidden');

                            $('.photo-popup-item').attr('src', '/post_photo/' + _container.postId + '/' + imgUrl);

                            that.methods.resizePopupPhoto();
                            $('#mCSB_2_container').css('left', 0);
                        },
                        changePopupPhoto: function (imgUrl) {
                            $('.photo-popup-item').attr('src', '/post_photo/' + _container.postId + '/' + imgUrl);
                            that.methods.resizePopupPhoto();
                        },
                        showErrorMessage: function (text) {
                            $().toastmessage('showToast', {
                                text     : text,
                                stayTime:  5000,
                                type     : 'error'
                            });
                        },
                        initContainer: function () {
                            if(_container.state.$photoBox === null) {
                                _container.state.$photoBox = $('.photo-popup');
                                _container.state.$sourceBox = $('.photo-popup-content .photo-source');
                                _container.state.$leftSliderButton = $('.photo-left-arrow div');
                                _container.state.$rightSliderButton = $('.photo-right-arrow div');
                            }
                        },
                        setSourceInPhoto: function () {
                            var source = _container.sources[_container.currentItem];
                            if (source !== null && source !== '') {
                                _container.state.$sourceBox.children().attr('href', source);
                                _container.state.$sourceBox.show();
                            } else {
                                _container.state.$sourceBox.hide();
                            }
                        },
                    },
                    init:function () {
                        $(document).ready(function () {
                            that.uploadPostPhotosHandler();
                            that.photoPopupWindowInit();
                        });
                    },
                    uploadPostPhotosHandler: function () {
                        if(main.User.is_guest === true) {
                            $(document).off('click', '.photo-upload-sign').on('click', '.photo-upload-sign', function (e) {
                                $('.sign_in_btn').trigger('click');
                                that.methods.showErrorMessage('Для добавления фото необходимо войти на сайт');
                                e.preventDefault();
                            });
                        } else {
                            $(document).off('change', '#post-photos').on('change', '#post-photos', function (e) {
                                if (uploads.validatePhotos(e.target.files)) {
                                    var form = new FormData();
                                    $.each(e.target.files, function (key, value) {
                                        form.append('post-photos[]', value);
                                    });
                                    form.append('postId', $(this).data('id'));
                                    uploads.uploadFiles('/post/upload-photo', form, that.successUploadPostPhotosHandler)
                                } else {
                                    that.methods.showErrorMessage('Изображение должно быть в формате JPG, GIF или PNG.' +
                                        ' Макс. размер файла: 15 МБ. Не более 10 файлов');
                                }
                                $(this).val('');
                            });
                        }
                    },
                    successUploadPostPhotosHandler: function (response) {
                        if(response.success) {
                            $.pjax.reload({
                                container: '#post-feeds',
                                push: true,
                                replace: true
                            });
                            $().toastmessage('showToast', {
                                text: 'Изображение загружено',
                                stayTime: 5000,
                                type: 'success'
                            });
                        } else {
                            that.methods.showErrorMessage('Изображение должно быть в формате JPG, GIF или PNG.' +
                                ' Макс. размер файла: 15 МБ.');
                        }
                    },

                    photoPopupWindowInit: function () {
                        $(document).ready(function () {
                            $(document).on('click','.block-photos .photo, .container-photo', function (e) {
                                if($(e.target).hasClass('avatar-user')) {
                                    return;
                                }
                                _container.currentItem = $(this).data('sequence');
                                var type = $(this).parent().data('type');
                                if(type === 'owner') {
                                    _container.loadToContainer = _container.loadPhotosFromPage;
                                    _container.url = null;
                                } else {
                                    _container.loadToContainer = _container.loadPhotosFromServer;
                                    if (_container.url === false) {
                                        _container.url = '/post/get-photos?type=' + type + '&postId=' + _container.postId;
                                        var perPage = parseInt(_container.currentItem) + 1;
                                        _container.url += '&per-page=' + perPage;

                                    }
                                }

                                var url = _container.current();
                                that.methods.setSourceInPhoto();
                                that.methods.showPopupPhoto(url);
                            });

                            $(document).on('click','.photo-right-arrow div,.next-popup-photo', function () {
                                if(_container.state.right) {
                                    var url = _container.next();
                                    that.methods.setSourceInPhoto();
                                    that.methods.changePopupPhoto(url);
                                }
                            });
                            $(document).on('click','.photo-left-arrow div,.pre-popup-photo', function () {
                                if(_container.state.left) {
                                    var url = _container.prev();
                                    that.methods.setSourceInPhoto();
                                    that.methods.changePopupPhoto(url);
                                }
                            });
                            $(window).keydown(function(e){
                                if(_container !== undefined && _container.state.$photoBox !== null) {
                                    switch (e.keyCode) {
                                        case 37: _container.state.$leftSliderButton.trigger('click'); break;
                                        case 39: _container.state.$rightSliderButton.trigger('click'); break;
                                        default: return false;
                                    }
                                }
                            });
                            $(window).bind('resize', $.debounce(250, that.methods.resizePopupPhoto));
                            $(document).on('click','.close-photo-popup', function () {
                                _container.resetState();
                                $('.container-blackout-photo-popup').css('display', 'none');
                                $('.photo-popup').css('display', 'none');
                                $('body').css('overflow', 'auto');
                            });
                            $('.photo-header').mCustomScrollbar({axis: "x",scrollInertia: 50, scrollbarPosition: "outside"});
                        })

                    },
                };

                return that
            }

        };

        return that;
    }

}(window,document,undefined,jQuery));

var post = Post();
post.info=post.Info();
post.init();
post.info.init();

post.photos = post.Photos();
post.photos.init();