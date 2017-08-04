/**
 * Created by jrborisov on 8.7.17.
 */
var News = (function (window, document, undefined,$) {

    return function () {
        var methods = {
            defineUrlByItemType: function (type) {
                switch (type) {
                    case 'post' : return '/post/favorite-state'; break;
                    case 'news' : return '/news/favorite-state'; break;
                }
            }
        };
        var that = {

            init: function () {
                $(document).ready(function () {
                    that.addToFavorite();
                });
            },
            addToFavorite: function () {
                $(document).on('click', '.bookmarks-btn', function () {
                    if(main.User.is_guest){
                        main.showErrorAut('Не авторизованые пользователи не могут оценивать записи');
                        return false;
                    }
                    var $container_replace = $(this);
                    var $block = $container_replace.closest('.card-block');
                    var item_id = $block.data('item-id');
                    var url = methods.defineUrlByItemType($block.data('type'));

                    that.sendRequsetForStateItem($container_replace,url, item_id);
                    return false;
                });

                $(document).on('click','.container-post .add-favorite',function () {
                    if(main.User.is_guest){
                        main.showErrorAut('Не авторизованые пользователи не могут оценивать записи');
                        return false;
                    }
                    var $container_replace = $(this);
                    var $block = $container_replace.closest('.container-post');
                    var item_id = $block.data('item-id');
                    var url = methods.defineUrlByItemType($block.data('type'));
                    that.sendRequsetForStateItem($container_replace,url, item_id);
                })
            },

            sendRequsetForStateItem: function ($container_replace,url, item_id) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {itemId: item_id},
                    dataType: "json",
                    async:false,
                    success:function (response) {
                        if(response.status=='error'){
                            $().toastmessage('showToast', {
                                text: response.message,
                                stayTime:5000,
                                type:'error'
                            });

                        }else {
                            $container_replace.text(response.count)
                            if(response.status=='add'){
                                $container_replace.addClass('active');
                            }else {
                                $container_replace.removeClass('active');
                            }
                        }
                    }
                });
            },

            Comments: function () {

                var __$container_to_which=null;
                var __$container_under_write = null;
                var __id_comment_to_which=null;

                var comments = {
                    init: function () {
                        $(document).ready(function () {

                           comments.setAutoResize('.textarea-main-comment');
                           $(document).off('click','.container-write-comments .large-wide-button')
                               .on('click', '.container-write-comments .large-wide-button',
                                   function () {
                                       if(!main.User.is_guest){
                                           if($(this).hasClass('main')){
                                               comments.closeUnderCommentForm();
                                           }
                                           comments.sendComment.apply(this);
                                       }else {
                                           main.showErrorAut('Не авторизованные пользователи не могут оставлять комментарии');
                                       }
                                   }
                               );
                            $(document).off('click','.textarea-main-comment')
                                .on('click','.textarea-main-comment',function (event) {
                                    if(main.User.is_guest){
                                        main.showErrorAut('Не авторизованные пользователи не могут оставлять комментарии');
                                    }else {
                                        comments.closeUnderCommentForm();
                                    }
                            });

                            $(document).off('click','.container-comment .btn-comment.btn-comm.reply')
                                .on('click','.container-comment .btn-comment.btn-comm.reply',function () {
                                    if(main.User.is_guest){
                                        main.showErrorAut('Не авторизованные пользователи не могут оставлять комментарии');
                                    }else {
                                        comments.openUnderCommentForm.apply(this);
                                    }
                                });

                            $(document).off('click','.container-comment .btn-comment.btn-comm.cancel')
                                .on('click','.container-comment .btn-comment.btn-comm.cancel',function () {
                                    comments.closeUnderCommentForm();
                                });

                            $(document).off('click','.container-comment .btn-comment.delete')
                                .on('click','.container-comment .btn-comment.delete',function () {
                                    if(main.User.is_guest){
                                        main.showErrorAut('Не авторизованные пользователи не могут удалять комментарии');
                                    }else {
                                        comments.deleteComment.apply(this);
                                    }
                                });
                            $(document).off('click','.container-comment .btn-comment.btn-like')
                                .on('click','.container-comment .btn-comment.btn-like',function () {
                                    if(main.User.is_guest){
                                        main.showErrorAut('Не авторизованные пользователи не могут оценивать комментарии');
                                    }else {
                                        comments.add_remove_like.apply(this);
                                    }
                                });
                            $(document).off('click','.container-comment .btn-comment.cplt')
                                .on('click','.container-comment .btn-comment.cplt',function () {
                                    if(main.User.is_guest){
                                        main.showErrorAut('Не авторизованные пользователи не могут оставлять жалобы на комментарии');
                                    }else {
                                        comments.showFormComplaint.apply(this);
                                    }
                                });
                        });
                    },

                    openUnderCommentForm:function () {
                        comments.closeUnderCommentForm();
                        __$container_to_which=$(this).parents('.container-comment');

                        var comment_id = __$container_to_which.data('comment_id');

                        var htmlContainerWrite = comments.getContainerWrite(comment_id);
                        if(htmlContainerWrite){
                            __$container_to_which.find('.btn-comment.btn-comm.reply')
                                .removeClass('reply').addClass('cancel').text('Отменить')
                            __$container_to_which.after(htmlContainerWrite);
                            __$container_to_which.addClass('neig');
                            __$container_under_write = __$container_to_which.next();
                            __id_comment_to_which=comment_id;
                            comments.toFocusWrite(__$container_under_write.find('textarea'));

                        }
                    },
                    closeUnderCommentForm:function () {
                        if(__$container_to_which!=null){
                            __$container_to_which.find('.btn-comment.btn-comm.cancel')
                                .removeClass('cancel').addClass('reply').text('Ответить');
                            __$container_to_which.removeClass('neig');
                            __$container_to_which=null;
                        }
                        if(__$container_under_write !=null){
                            __$container_under_write.remove();
                            __$container_under_write=null;
                        }
                        __id_comment_to_which=null;


                    },
                    getContainerWrite:function(id_comment){
                        if(comments.getContainerWrite.cache==undefined)
                            comments.getContainerWrite.cache = {};

                        if(comments.getContainerWrite.cache[id_comment]==undefined){
                            $.ajax({
                                url: '/news/get-container-write-comment?id='+id_comment,
                                type: "GET",
                                async:false,
                                success: function (response) {
                                    if(response){
                                        comments.getContainerWrite.cache[id_comment] = response;
                                    }else {
                                        comments.getContainerWrite.cache[id_comment] = undefined;
                                    }

                                }
                            });
                        }
                        return comments.getContainerWrite.cache[id_comment];
                    },
                    toFocusWrite: function ($container) {
                        $container.attr('id','tempBlockWrite');
                        var count_char= $container.val().length;
                        main.setSelectionRange(document.getElementById('tempBlockWrite'),count_char,count_char)
                        comments.setAutoResize('#tempBlockWrite');
                    },
                    setAutoResize:function (selector) {
                        $(selector).autosize()
                    },
                    sendComment:function () {
                       var object_send ={
                           data:'',
                           news_id:null
                       };

                       object_send.data= $(this).parents('.container-write-comments').find('textarea').val();
                       object_send.news_id = parseInt($('#comments_news_container').data('news_id'));
                       if(__id_comment_to_which!=null){
                           object_send.comment_id=__id_comment_to_which;
                       }

                        $.ajax({
                            url: '/news/add-comments',
                            type: "POST",
                            dataType: "json",
                            data:object_send,
                            success: function (response) {
                                if(response.status=='error'){
                                    $().toastmessage('showToast', {
                                        text: response.message,
                                        stayTime:5000,
                                        type:'error'
                                    });

                                }else {
                                    comments.reloadComments(object_send.news_id,$('#comments_news_container'));
                                }
                            }
                        });

                    },
                    reloadComments:function (news_id,$bloc_replace) {
                        var count_view_comments= comments.getCountViewComments();
                        $.ajax({
                            url: '/news/reload-comments?per-page='+count_view_comments+'&id='+news_id,
                            type: "get",
                            success: function (response) {
                               if(response){
                                   $bloc_replace.html(response)
                                   comments.setAutoResize('.textarea-main-comment');
                               }
                            }
                        });
                    },
                    getCountViewComments:function () {
                        return $('.container-comment.main','.container-comments').length;
                    },
                    deleteComment:function () {
                        object_send={
                            id:null,
                            news_id:null
                        }

                        object_send.id=$(this).parents('.container-comment').data('comment_id');
                        object_send.news_id = parseInt($('#comments_news_container').data('news_id'));

                        $.ajax({
                            url: '/news/delete-comment',
                            type: "POST",
                            dataType: "json",
                            data:object_send,
                            success: function (response) {
                                if(response.status=='error'){
                                    $().toastmessage('showToast', {
                                        text: response.message,
                                        stayTime:5000,
                                        type:'error'
                                    });

                                }else {
                                    comments.reloadComments(object_send.news_id,$('#comments_news_container'));
                                }
                            }
                        });
                    },
                    add_remove_like:function () {

                        object_send={
                            id:null
                        };

                        object_send.id=$(this).parents('.container-comment').data('comment_id');
                        var $container_like=$(this);

                        $.ajax({
                            url: '/news/add-remove-like-comment',
                            type: "GET",
                            dataType: "json",
                            data:object_send,
                            success: function (response) {
                                if(response.status=='error'){
                                    $().toastmessage('showToast', {
                                        text: response.message,
                                        stayTime:5000,
                                        type:'error'
                                    });

                                }else {
                                    if(response.status=='add'){
                                        $container_like.addClass('active').text(response.count);
                                    }else {
                                        $container_like.removeClass('active').text(response.count);
                                    }
                                }
                            }
                        });
                    },
                    showFormComplaint:function () {
                        var html = main.getFormComplaint();
                        var id_comment = $(this).parents('.container-comment').data('comment_id');
                        var news_id =parseInt($('#comments_news_container').data('news_id'));
                        $('.container-blackout-popup-window').html(html).show();
                        $('.container-blackout-popup-window .form-complaint .complain-btn').off('click')
                            .on('click',function () {
                                var message = $('.container-blackout-popup-window .form-complaint input[name="complain"]').val();
                                $.ajax({
                                    url: '/news/complain-comment',
                                    type: "POST",
                                    dataType: "json",
                                    data:{id:id_comment,message:message},
                                    success: function (response) {
                                        if(response.status=='error'){
                                            $().toastmessage('showToast', {
                                                text: response.message,
                                                stayTime:5000,
                                                type:'error'
                                            });

                                        }else {
                                            main.closeFormComplaint();

                                            $().toastmessage('showToast', {
                                                text: response.message,
                                                stayTime:8000,
                                                type:'success'
                                            });
                                            comments.reloadComments(news_id,$('#comments_news_container'));
                                        }
                                    }
                                });
                            })

                    }

                };

                return comments;
            }

        }

        return that;
    }

}(window,document,undefined,jQuery));

var news = News();
var newsComments = news.Comments();
news.init();
newsComments.init();

