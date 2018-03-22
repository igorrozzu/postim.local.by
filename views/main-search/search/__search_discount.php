<div class="block-content">
    <?php if($widget['params']['dataprovider']->totalCount):?>
        <div class="cards-block-discount row-4 main-pjax" data-favorites-state-url="/discount/favorite-state">

            <?=$widget['class']::widget($widget['params']);?>

        </div>
        <div class="clear-fix"></div>
        <div style="margin-top: 30px"></div>
    <?php else:?>
        <div class="container-message">
            <div class="message-filter">
                <p>По вашему запросу ничего не найдено</p>
                <span>Попробуйте использовать другие ключевые слова</span>
            </div>
        </div>
    <?php endif;?>
</div>

