<div class="container-write-comments">
    <div class="main-write-comment" >
        <?php if(Yii::$app->user->isGuest):?>
            <img class="profile-icon60x60" src="img/default-profile-icon.png">
        <?php else:?>
            <img class="profile-icon60x60" src="<?= Yii::$app->user->identity->getPhoto()?>">
        <?php endif;?>
        <textarea placeholder="Что скажете по этому поводу?" class="textarea-main-comment"></textarea>
    </div>
    <div class="large-wide-button main"><p>Написать комментарий</p></div>
</div>
<?php if($dataprovider->getTotalCount()):?>
<div class="container-comments">
    <?php
        echo $this->render('item_comment', [
            'dataprovider' => $dataprovider,
        ])
    ?>
</div>
<?php endif;?>