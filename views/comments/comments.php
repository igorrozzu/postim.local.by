<?php

use app\components\commentsWidget\CommentsNewsWidget;

?>
<h2 class="h2-c">Комментарии <span class="total"><?= $totalComments ?></span></h2>
<div class="block-сomments-post">
    <?= CommentsNewsWidget::widget(['dataprovider' => $dataProviderComments]) ?>
</div>