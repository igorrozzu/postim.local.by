<div class="container-write-comments under">
    <div class="under-write-comment" >
        <div class="profile-write-comment">
            <?php if(Yii::$app->user->isGuest):?>
                <img class="profile_under_comment" src="img/default-profile-icon.png">
            <?php else:?>
                <img class="profile_under_comment" src="<?= Yii::$app->user->identity->getPhoto()?>">
            <?php endif;?>
            <div class="user-name"><?=Yii::$app->user->identity->name?> <?=Yii::$app->user->identity->surname?></div>
        </div>
        <textarea placeholder="Что скажете по этому поводу?" class="textarea-comment textarea-under-comment"><?=$comment->user->name?> <?=$comment->user->surname?>, </textarea>
    </div>
    <div class="large-wide-button"><p>Написать комментарий</p></div>
</div>