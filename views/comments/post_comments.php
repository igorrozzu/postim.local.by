<?php
use app\components\commentsWidget\QuestionAnswerWidget;
?>
<div class="block-content-between cust">
    <h2 class="h2-v">Вопросы и ответы <span class="total"><?=$totalComments?></span></h2>
    <noindex>
        <p class="text p-text">Задайте вопрос — владельцы мест, пользователи и редакция помогут найти ответ</p>
    </noindex>
</div>

<div class="block-сomments-post" style="margin-top: 10px">
    <?=QuestionAnswerWidget::widget(['dataprovider'=>$dataProviderComments])?>
</div>