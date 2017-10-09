<?php
use app\components\commentsWidget\CommentsPostsWidget;
?>
<div class="comments_entity_container" data-entity_id="<?=$id?>">
    <div class="block-сomments-post _reviews">
		<?=CommentsPostsWidget::widget(['dataprovider'=>$dataProviderComments,
            'totalComments'=> $totalComments,
            'is_official_user'=>$is_official_user??false
        ])?>
    </div>
</div>
