<?php foreach ($underComments as $item)?>
<div class="container-comment">
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
        <div class="comment-text"><?=$item->data?></div>
        <div class="btns-comment">
            <div class="btn-comment btn-like"><?=$item->like?></div>
            <div class="btn-comment btn-comm">Ответить</div>
            <div class="btn-comment">Пожаловаться</div>
        </div>
    </div>
</div>