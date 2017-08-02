<?php
$model = $dataprovider->getModels();
foreach ($model as $item):
?>
<div class="container-comment main" data-comment_id="<?=$item->id?>">
    <div class="profile-commentator">
        <img class="profile-icon-commentator" src="<?=$item->user->getPhoto()?>">
    </div>
    <div class="comment-content">
        <div class="comment-content-header">
            <div class="content-between">
                <p class="user-name"><?=$item->user->name?> <?=$item->user->surname?></p>
                <div class="user-level"><?=$item->user->userInfo->level?> <span>&nbsp;уровень</span></div>
            </div>
            <span class="comment-time"><?=Yii::$app->formatter->printDate($item->date)?></span>
        </div>
        <div class="comment-text"><?=\yii\helpers\Html::encode($item->data)?></div>
        <div class="btns-comment">
            <div class="btn-comment btn-like <?=$item->is_like?'active':''?>"><?=$item->like?></div>
            <div class="btn-comment btn-comm reply">Ответить</div>
            <?php if(Yii::$app->user->isGuest || $item->user->id!=Yii::$app->user->id):?>
                <?php if(!$item->is_complaint):?>
                    <div class="btn-comment cplt">Пожаловаться</div>
                <?php endif;?>
            <?php elseif(!$item->underComments):?>
                <div class="btn-comment delete">Удалить</div>
            <?php endif;?>
        </div>
    </div>
</div>
<?php
    if($item->underComments){
        echo $this->render('item_under_comments',[
            'underComments'=>$item->underComments,
        ]);
    }
?>
<?php endforeach; ?>

<?php if ($hrefNext = $dataprovider->pagination->getLinks()['next'] ?? false): ?>
    <div class="replace-block" id="news-comments">
        <div class="btn-show-more" data-selector_replace="#news-comments" data-href="<?=$hrefNext?>">Показать больше комментариев</div>
    </div>
<?php endif; ?>
