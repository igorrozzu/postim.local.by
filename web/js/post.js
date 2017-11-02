var Post = (function (window, document, undefined,$) {

    return function () {

        var that = {

            init:function () {
                that.transitionToPostHandler();

                $(document).off('click','.href-edit')
                    .on('click','.href-edit',function (e) {
						if (main.User.is_guest) {
							main.showErrorAut('Незарегистрированные пользователи не могут редактировать место');
							e.preventDefault();
							return false;
						}
					})
            },
            transitionToPostHandler: function () {
                $(document).off('click','.card-block').on('click','.card-block', function (e) {
                    if(!$(e.target).is('a')) {
                        e.stopPropagation();
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

                };

                return info
            },
            Photos: function () {
                var _container = {
                    url: false,
                    currentItem: -1,
                    loadTime: null,
                    postId: null,
                    userId: null,
                    reviewId: null,
                    data: [],
                    postInfo: [],
                    type: null,
                    imgSize: {height: null, width: null},
                    allPhotoCount: null,
                    state: {
                        $photoBox: null,
                        $wrapPhotoBox: null,
                        $sourceBox: null,
                        $titleBox: null,
                        $leftSliderButton: null,
                        $rightSliderButton: null,
                        $photoCounter: {
                            start: null,
                            end: null,
                        },
                        left: true,
                        right: true,
                        isAnimationRun: false,
                        galleryIsOpen: false,
                        isChangeTitleInSlider: false,
                    },

                    next: function () {
                        if (_container.currentItem === 0) {
                            _container.state.$photoBox.removeClass('prev-btn-hide');
                            _container.state.left = true;
                        }
                        _container.currentItem++;
                        if (_container.data[_container.currentItem + 1] === undefined) {
                            _container.loadPhotosFromServer();
                        }
                        if (_container.data[_container.currentItem + 1] === undefined && _container.url === null) {
                            _container.state.right = false;
                            _container.state.$photoBox.addClass('next-btn-hide');
                        }
                        return _container.data[this.currentItem];

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
                        return _container.data[this.currentItem];
                    },

                    current: function () {
                        that.methods.initContainer();

                        if(_container.data[_container.currentItem] === undefined) {
                            _container.loadPhotosFromServer();
                        }
                        if(_container.currentItem === 0) {
                            _container.state.$photoBox.addClass('prev-btn-hide');
                            _container.state.left = false;
                        }
                        if(_container.data[_container.currentItem + 1] === undefined && _container.url === null) {
                            _container.state.$photoBox.addClass('next-btn-hide');
                            _container.state.right = false;
                        }
                        return _container.data[this.currentItem];

                    },

                    getPhotoSequenceById: function (id) {
                        for (var i = 0; i < _container.data.length; i++) {
                            if (_container.data[i].id === id) {
                                return i;
                            }
                        }

                        return null;
                    },

                    loadPhotosFromPage: function () {
                        if(_container.data.length === 0) {
                            $('.block-photos-owner .container-photo').each(function (index) {
                                var img = $(this).css('background-image');
                                var match = img.match(/post_photo\/\d+\/(.+)"\)$/);
                                _container.data[index] = {
                                    link: match[1],
                                    source: $(this).data('source'),
                                    id: $(this).data('id'),
                                    user_status: $(this).data('status')
                                };
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
                                    _container.data = _container.data.concat(response.data);
                                    if(response.postInfo !== undefined) {
                                        _container.postInfo = _container.postInfo.concat(response.postInfo);
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
                        _container.state.galleryIsOpen = false;
                    },
                };

                var that = {
                    resetContainer: function () {
                        that.methods.resetContainerData();
                        _container.state.isChangeTitleInSlider = false;
                    },

                    setAllPhotoCount: function (count) {
                        _container.allPhotoCount = count;
                    },

                    setLoadTime: function (loadTime) {
                        _container.loadTime = loadTime;
                    },

                    setPostId: function (postId) {
                        _container.postId = postId;
                    },

                    isChangeTitleInSlider: function (value) {
                        _container.state.isChangeTitleInSlider = value;
                    },

                    setUserId: function (userId) {
                        _container.userId = userId;
                    },

                    initPhotoSlider: function (params) {
                        _container.type = params.type || 'all';
                        _container.reviewId = params.reviewId;

                        if (_container.type === 'profile') {
                            _container.url = '/user/get-photos?type=' + _container.type +
                                '&userId=' + _container.userId + '&photo_id=' + params.photoId;
                        } else if (_container.type === 'review') {
                            _container.url = '/photo/get-for-review?review_id=' + _container.reviewId;
                        } else {
                            _container.url = '/post/get-photos?type=' + _container.type +
                                '&postId=' + _container.postId + '&photo_id=' + params.photoId;
                        }

                        $.ajax({
                            url: _container.url,
                            dataType: 'json',
                            async: false,
                            success: function (response) {
                                _container.url = response.url;
                                _container.data = _container.data.concat(response.data);

                                _container.currentItem = response.sequence ||
                                    _container.getPhotoSequenceById(parseInt(params.photoId));

                                if (response.postInfo !== undefined) {
                                    _container.postInfo = _container.postInfo.concat(response.postInfo);
                                }
                            }
                        });

                        var photo = _container.current();
                        that.methods.setSourceInPhoto();
                        that.methods.setTitleInPhoto();
                        that.methods.setCounterInPhoto();
                        that.methods.showPopupPhoto(photo);
                        that.photoPopupHandlers();
                    },

                    methods: {
                        resetContainerData: function () {
                            _container.data = [];
                            _container.postInfo = [];
                            _container.url = false;
                        },

                        resizePopupPhoto: function () {
                            var $photoPopup = $('.photo-popup');
                            if ($photoPopup.length !== 0 && $photoPopup.css('display') !== 'none') {
                                var wrapHeight = _container.state.$wrapPhotoBox.height();
                                var wrapWidth = _container.state.$wrapPhotoBox.width();
                                var $img = $(".photo-wrap img");
                                $img.css({
                                    maxWidth: wrapWidth + 'px',
                                    maxHeight: wrapHeight + 'px'
                                });
                            }
                        },

                        showPopupPhoto: function (photo) {
                            $('.container-blackout-photo-popup').css('display', 'block');
                            _container.state.$photoBox.css('display', 'flex');
                            $('body').css('overflow', 'hidden');

                            $('.photo-popup-item').attr('src', that.methods.createPhotoUrl(photo));

                            that.methods.resizePopupPhoto();
                            $('.photo-info .mCSB_container').css('left', 0);
                            _container.state.galleryIsOpen = true;
                        },

                        changePopupPhoto: function (photo) {
                            $('.photo-popup-item').attr('src', that.methods.createPhotoUrl(photo));
                            that.methods.resizePopupPhoto();
                        },

                        createPhotoUrl: function (photo) {
                            if(_container.type === 'profile' || _container.type === 'review') {
                                return '/post_photo/' + photo.post_id + '/' + photo.link;
                            } else {
                                return '/post_photo/' + _container.postId + '/' + photo.link;
                            }
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
                                _container.state.$wrapPhotoBox = $('.photo-popup .photo-wrap');
                                _container.state.$sourceBox = $('.wrap-photo-info .photo-source');
                                _container.state.$titleBox = $('.photo-popup .photo-header a');
                                _container.state.$leftSliderButton = $('.photo-left-arrow div');
                                _container.state.$rightSliderButton = $('.photo-right-arrow div');
                                _container.state.$photoCounter.start = $('.gallery-counter #start-photo-counter');
                                _container.state.$photoCounter.end = $('.gallery-counter #end-photo-counter');
                            }
                        },

                        setSourceInPhoto: function () {
                            var source = _container.data[_container.currentItem].source;
                            if (source !== null && source !== '') {
                                _container.state.$sourceBox.children('a').attr('href', source);
                                _container.state.$sourceBox.show();
                            } else {
                                _container.state.$sourceBox.hide();
                            }
                        },

                        setTitleInPhoto: function () {
                            if (_container.state.isChangeTitleInSlider) {
                                var url = _container.postInfo[_container.currentItem].url;
                                var title = _container.postInfo[_container.currentItem].title;
                                var postId = _container.data[_container.currentItem].post_id;
                                _container.state.$titleBox.attr('href', url + '-p' + postId);
                                _container.state.$titleBox.text(title);
                            }
                        },

                        setCounterInPhoto: function () {
                            _container.state.$photoCounter.start.text(_container.currentItem + 1);
                            if (_container.type === 'review') {
                                _container.state.$photoCounter.end.text(_container.data.length);
                            } else {
                                _container.state.$photoCounter.end.text(_container.allPhotoCount);
                            }
                        },

                        changeUrlOfPhoto: function () {
                            var url;
                            var item = _container.data[_container.currentItem];

                            if (_container.type === 'profile') {
                                url = '/id' + item.user_id + '?photo_id=' + item.id;
                            } else if (_container.type === 'review') {
                                url = location.pathname + '?review_id=' + _container.reviewId +
                                    '&photo_id=' + item.id
                            } else {
                                url = location.pathname + '?photo_id=' + item.id + '-' + item.user_status;
                            }

                            if (history.state !== null) {
                                history.replaceState({}, url, url);
                            } else {
                                history.pushState({}, url, url);
                            }
                        },
                    },
                    init:function () {
                        $(document).ready(function () {
                            that.uploadPostPhotosHandler();
                            that.photoPopupWindowInit();
                            that.sendComplaintHandler();
                        });
                    },

                    sendComplaintHandler: function () {
                        $(document).on('click', '.complain-gallery-text', function () {
                            if (main.User.is_guest) {
								main.showErrorAut('Незарегистрированные пользователи не могут жаловаться на фото');
                                return;
                            }

                            var photo_id = _container.data[_container.currentItem].id;
                            var type_photo = 1;
                            main.initFormComplaint(photo_id,type_photo,undefined);
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
                            _container.data = [];
                            _container.url = false;
                        } else {
                            that.methods.showErrorMessage('Изображение должно быть в формате JPG, GIF или PNG.' +
                                ' Макс. размер файла: 15 МБ.');
                        }
                    },

                    photoPopupWindowInit: function () {
                        $(document).ready(function () {
                            $(document).on('click','.block-photos .photo, .container-photo, .review-photo', function (e) {
                                if ($(e.target).hasClass('avatar-user')) {
                                    return;
                                }

                                _container.currentItem = $(this).data('sequence');
                                _container.type = $(this).parent().data('type');

                                if(_container.type === 'review') {
                                    _container.reviewId = $(this).parent().data('reviews_id');
                                }

                                _container.loadPhotosFromPage();

                                if (_container.url === false) {
                                    if (_container.type === 'profile') {
                                        _container.url = '/user/get-photos?type=' + _container.type +
                                            '&userId=' + _container.userId;
                                    } else if (_container.type === 'review') {
                                        _container.url = '/photo/get-for-review?review_id=' + _container.reviewId;
                                    } else {
                                        _container.url = '/post/get-photos?type=' + _container.type +
                                            '&postId=' + _container.postId;
                                        var perPage = parseInt(_container.currentItem) + 1 - _container.data.length;
                                        _container.url += '&per-page=' + perPage;
                                    }
                                }

                                var photo = _container.current();
                                that.methods.setSourceInPhoto();
                                that.methods.setTitleInPhoto();
                                that.methods.setCounterInPhoto();
                                that.methods.showPopupPhoto(photo);
                                that.methods.changeUrlOfPhoto();
                            });
                            that.photoPopupHandlers();
                        })

                    },
                    photoPopupHandlers: function () {

                        $(document).on('click','.photo-right-arrow div', function () {
                            if(_container.state.right && !_container.state.isAnimationRun) {
                                _container.state.isAnimationRun = true;
                                _container.state.$wrapPhotoBox.animate({"left": "-100%"}, 300, null, function () {
                                    var photo = _container.next();
                                    that.methods.setSourceInPhoto();
                                    that.methods.setTitleInPhoto();
                                    that.methods.setCounterInPhoto();
                                    that.methods.changePopupPhoto(photo);
                                    that.methods.changeUrlOfPhoto();
                                    _container.state.$wrapPhotoBox.css({
                                        left: '100%'
                                    });
                                    _container.state.$wrapPhotoBox.animate({"left": "0px"}, 300, function () {
                                        _container.state.isAnimationRun = false;
                                    });
                                });
                            }
                        });

                        $(document).on('click','.photo-left-arrow div', function () {
                            if(_container.state.left && !_container.state.isAnimationRun) {
                                _container.state.isAnimationRun = true;
                                _container.state.$wrapPhotoBox.animate({"left": "100%"}, 300, null, function () {
                                    var photo = _container.prev();
                                    that.methods.setSourceInPhoto();
                                    that.methods.setTitleInPhoto();
                                    that.methods.setCounterInPhoto();
                                    that.methods.changePopupPhoto(photo);
                                    that.methods.changeUrlOfPhoto();
                                    _container.state.$wrapPhotoBox.css({
                                        left: '-100%'
                                    });
                                    _container.state.$wrapPhotoBox.animate({"left": "0px"}, 300, function () {
                                        _container.state.isAnimationRun = false;
                                    });
                                });
                            }
                        });

                        $(window).keydown(function(e){
                            if(_container !== undefined && _container.state.$photoBox !== null) {
                                switch (e.keyCode) {
                                    case 37: that.prevPhoto(); break;
                                    case 39: that.nextPhoto(); break;
                                }
                            }
                        });

                        $(window).bind('resize', $.debounce(250, that.methods.resizePopupPhoto));
                        $(document).on('click','.close-photo-popup', function () {
                            _container.resetState();
                            that.methods.resetContainerData();
                            $('.container-blackout-photo-popup').css('display', 'none');
                            $('.photo-popup').css('display', 'none');
                            $('body').css('overflow', 'auto');

                            history.replaceState({}, location.pathname, location.pathname);
                        });

                        $(window).on('popstate', function (e) {
                            if (_container !== undefined && _container.state.galleryIsOpen) {
                                $('.close-photo-popup').trigger('click');
                            }
                        });
                    },

                    nextPhoto: function () {
                        _container.state.$rightSliderButton.trigger('click');
                    },

                    prevPhoto: function () {
                        _container.state.$leftSliderButton.trigger('click');
                    },

                };

                return that
            },

            BusinessAccount: function () {

                var businessAccount = {

                    init: function () {
                        $(document).ready(function () {
                            $(document).off('click','.block-info-for-owner')
                                .on('click','.block-info-for-owner',function () {
                                    if(!main.User.is_guest){
                                        var post_id = $(this).data('post_id');
                                        businessAccount.initForm(post_id);
                                    }else {
                                        main.showErrorAut('Незарегистрированные пользователи не могут оставить заявку на бизнес-аккаунт.');
                                    }

                                })
                        });
                    },

                    initForm:function (post_id) {
                        var html = main.getFormEntities('/post/get-form-business-account?post_id='+post_id);
                        $('.container-blackout-popup-window').html(html).show();

                        businessAccount.initEventForm();

                    },

                    clearEventsForm:function () {

                        $('.container-popup-window')
                            .off('click','.close-business-account-btn');
                        $('.container-popup-window')
                            .off('click','.create-business-account-btn');

                    },
                    initEventForm: function () {

                        $('.container-popup-window').off('click','.close-business-account-btn')
                            .on('click','.close-business-account-btn',function () {
                                businessAccount.closeForm()
                            });

                        $('.container-popup-window').off('click','.create-business-account-btn')
                            .on('click','.create-business-account-btn',function () {
                                var $form = $(this).parents('form');
                                businessAccount.sendForm($form);
                            });


                    },

                    sendForm : function ($form) {
                        var data = $form.serialize();

                        $.post('/post/save-business-account',data,function (response) {

                            if(response.success){

                                $().toastmessage('showToast', {
                                    text: response.message,
                                    stayTime:10000,
                                    type: 'success'
                                });

                                businessAccount.closeForm();

                            }else {
                                if(response.html){

                                    if(response.message){
                                        $().toastmessage('showToast', {
                                            text: response.message,
                                            stayTime:5000,
                                            type: 'error'
                                        });
                                    }

                                    $('.container-blackout-popup-window').html(response.html).show();
                                    businessAccount.initEventForm();
                                }
                            }

                        })

                    },


                    closeForm:function () {

                        $('.form-business-account').remove();
                        $('.container-blackout-popup-window').hide();
                        businessAccount.clearEventsForm();
                    }



                }
                return businessAccount;

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

post.business = post.BusinessAccount();
post.business.init();