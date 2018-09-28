<div class="container-write-comments">
    <div class="main-write-comment">
        <div class="profile-write-comment">
            <?php if (Yii::$app->user->isGuest): ?>
                <img class="profile-icon60x60" src="img/default-profile-icon.png">
                <div class="user-name">Гость</div>
            <?php else: ?>
                <img class="profile-icon60x60" src="<?= Yii::$app->user->identity->getPhoto() ?>">
                <div class="user-name"><?= Yii::$app->user->identity->name . ' ' . Yii::$app->user->identity->surname ?></div>
            <?php endif; ?>

        </div>
        <textarea placeholder="Что скажете по этому поводу?" class="textarea-comment textarea-main-comment"></textarea>
    </div>
    <div class="large-wide-button main"><p>Написать комментарий</p></div>
</div>
<?php if ($dataprovider->getTotalCount()): ?>
    <div class="container-comments">
        <?php
        echo $this->render('item_comment', [
            'dataprovider' => $dataprovider,
        ])
        ?>
    </div>
<?php endif; ?>