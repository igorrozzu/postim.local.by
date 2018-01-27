<?php
use app\components\commentsWidget\QuestionDiscountWidget;
?>
<h2 class="h2-v">Вопросы <span class="total"><?=$totalComments?></span></h2>
<div class="block-сomments-post" style="margin-top: 30px">
    <?=QuestionDiscountWidget::widget(['dataprovider'=>$dataProviderComments])?>
</div>