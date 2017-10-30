/**
 * Created by jrborisov on 8.7.17.
 */
var Comments = (function (window, document, undefined,$) {

    return function () {

		var __$container_to_which=null;
		var __$container_under_write = null;
		var __id_comment_to_which=null;
		var __id_controller = 1;

		var comments = {
			init: function (id_controller) {

				if(!!id_controller){
					__id_controller = id_controller;
				}

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
									main.showErrorAut('Неавторизованные пользователи не могут оставлять комментарии');
								}
							}
						);
					$(document).off('click','.textarea-main-comment')
						.on('click','.textarea-main-comment',function (event) {
							if(main.User.is_guest){
								main.showErrorAut('Неавторизованные пользователи не могут оставлять комментарии');
							}else {
								comments.closeUnderCommentForm();
							}
						});

					$(document).off('click','.container-comment .btn-comment.btn-comm.reply')
						.on('click','.container-comment .btn-comment.btn-comm.reply',function () {
							if(main.User.is_guest){
								main.showErrorAut('Неавторизованные пользователи не могут оставлять комментарии');
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
								main.showErrorAut('Неавторизованные пользователи не могут удалять комментарии');
							}else {
								comments.deleteComment.apply(this);
							}
						});
					$(document).off('click','.container-comment .btn-comment.btn-like')
						.on('click','.container-comment .btn-comment.btn-like',function () {
							if(main.User.is_guest){
								main.showErrorAut('Неавторизованные пользователи не могут оценивать комментарии');
							}else {
								comments.add_remove_like.apply(this);
							}
						});
					$(document).off('click','.container-comment .btn-comment.cplt')
						.on('click','.container-comment .btn-comment.cplt',function () {
							if(main.User.is_guest){
								main.showErrorAut('Неавторизованные пользователи не могут оставлять жалобы на комментарии');
							}else {
								comments.showFormComplaint.apply(this);
							}
						});

					$(document).off('click','.sign-official-answer')
						.on('click','.sign-official-answer',function () {
							if($(this).hasClass('active')){
								$(this).removeClass('active');
							}else {
								$(this).addClass('active');
							}
						})
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
						url: '/comments/get-container-write-comment?id='+id_comment,
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
					entity_id:null,
					type_entity:__id_controller
				};

				object_send.data= $(this).parents('.container-write-comments').find('textarea').val();
				object_send.entity_id = parseInt($(this).parents('.comments_entity_container').data('entity_id'));

				var $container_replace = $(this).parents('.comments_entity_container');
				if(__id_comment_to_which!=null){
					object_send.comment_id=__id_comment_to_which;
				}

				var $containerBtnHide = null;
				if(__id_controller == 2){
					$containerBtnHide = $(this).parents('.block-reviews').find('.review-footer-btn.hide-comm');
					if($(this).parents('.block-reviews').find('.sign-official-answer.active').length){
						object_send.official_answer = true;
					}
				}

				$.ajax({
					url: '/comments/add-comments',
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
							if($containerBtnHide!=null){
								$containerBtnHide.data('text',Number($containerBtnHide.data('text'))+1);
							}
							comments.reloadComments(object_send.entity_id,$container_replace);
						}
					}
				});

			},
			reloadComments:function (entity_id,$bloc_replace) {
				var count_view_comments= comments.getCountViewComments();
				$.ajax({
					url: '/comments/reload-comments?per-page='+count_view_comments+'&id='+entity_id+'&type_entity='+__id_controller,
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
					entity_id:null
				}

				object_send.id=$(this).parents('.container-comment').data('comment_id');
				object_send.entity_id = parseInt($(this).parents('.comments_entity_container').data('entity_id'));

				var $containerBtnHide = null;
				var $containerReplace = $(this).parents('.comments_entity_container');
				if(__id_controller == 2){
					$containerBtnHide = $(this).parents('.block-reviews').find('.review-footer-btn.hide-comm')
				}

				$.ajax({
					url: '/comments/delete-comment',
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
							if($containerBtnHide!=null){
								$containerBtnHide.data('text',Number($containerBtnHide.data('text'))-1);
							}
							comments.reloadComments(object_send.entity_id,$containerReplace);
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
					url: '/comments/add-remove-like-comment',
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
				var id_comment = $(this).parents('.container-comment').data('comment_id');
				var entity_id =parseInt($(this).parents('.comments_entity_container').data('entity_id'));
				var $containerReplace = $(this).parents('.comments_entity_container');
				var typeComment = 3;
                main.initFormComplaint(id_comment,typeComment,function () {
                    comments.reloadComments(entity_id,$containerReplace);
                });

			}

		};

		return comments;
    }

}(window,document,undefined,jQuery));

var comments = Comments();
