<div class="container-write-comments" style="border-top: 1px solid #d3e4ff;">
    <div class="main-write-comment">
        <div class="profile-write-comment">
            <img class="profile-icon60x60" src="<?= Yii::$app->user->getPhoto() ?>">
            <div class="user-name"><?= Yii::$app->user->getName() ?></div>
        </div>
        <textarea placeholder="Что скажете по этому поводу?" class="textarea-comment textarea-main-comment"></textarea>
    </div>
    <?php if ($is_official_user): ?>
        <div class="sign-official-answer">Официальный ответ</div>
    <?php endif; ?>
    <div class="large-wide-button main"><p>Написать комментарий</p></div>
</div>
<?php if ($dataprovider->getTotalCount()): ?>
    <div class="container-comments">
        <h2 class="h2-cr">Комментарии <span class="total"><?= $totalComments ?></span></h2>
        <?php
        echo $this->render('item_comment', [
            'dataprovider' => $dataprovider,
        ])
        ?>
    </div>
<?php endif; ?>