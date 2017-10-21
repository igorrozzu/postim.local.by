<?php if($dataAutoComplete && is_array($dataAutoComplete)):?>
    <div class="container-header-auto-complete">
        <div class="block-auto-complete-header">Места</div>
    </div>
    <div class="container-body-auto-complete main-pjax">
    <?php foreach ($dataAutoComplete as $item):?>
        <a class="a-auto-complete" href="/<?=$item['url_name'].'-p'.$item['id']?>"><?=$item['data']?></a>
    <?php endforeach;?>
    </div>
<?php endif;?>
<?php if($dataAutoCompleteNews && is_array($dataAutoCompleteNews)):?>
    <div class="container-header-auto-complete">
        <div class="block-auto-complete-header">Новости</div>
    </div>
    <div class="container-body-auto-complete main-pjax">
        <?php foreach ($dataAutoCompleteNews as $item):?>
            <a class="a-auto-complete" href="/<?=$item['url_name'].'-n'.$item['id']?>"><?=$item['header']?></a>
        <?php endforeach;?>
    </div>
<?php endif;?>
