<div class="container-write-comments under">
    <div class="under-write-comment" >
        <?php if(Yii::$app->user->isGuest):?>
            <img class="profile_under_comment" src="img/default-profile-icon.png">
        <?php else:?>
            <img class="profile_under_comment" src="<?= Yii::$app->user->identity->getPhoto()?>">
        <?php endif;?>
        <textarea placeholder="Что скажете по этому поводу?" class="textarea-under-comment"><?=$comment->user->name?> <?=$comment->user->surname?>, </textarea>
    </div>
    <div class="large-wide-button"><p>Написать комментарий</p></div>
</div>