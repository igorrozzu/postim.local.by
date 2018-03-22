<div class="block-content">
    <?php if($widget['params']['dataprovider']->getTotalCount()):?>
        <div class="cards-block">
            <?=$widget['class']::widget($widget['params']);?>
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
