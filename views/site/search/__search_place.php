<div class="block-content">
<?php if($widget_params['dataprovider']->totalCount):?>
    <div class="cards-block">
        <?=$widget::widget($widget_params);?>
    </div>
<?php else:?>
    <div class="container-message">
        <div class="message-filter">
            <p>По вашему запросу ничего не найдено</p>
            <span>Попробуйте использовать другие ключевые слова</span>
        </div>
    </div>
<?php endif;?>
</div>
