<?php if(isset($entities['post']) && is_array($entities['post'])):?>
    <div class="container-header-auto-complete">
        <div class="block-auto-complete-header">Места</div>
    </div>
    <div class="container-body-auto-complete main-pjax">
    <?php foreach ($entities['post'] as $item):?>
        <a class="a-auto-complete" href="/<?=$item['url_name'].'-p'.$item['id']?>">
            <?=$item['data']?>
        </a>
    <?php endforeach;?>
    </div>
<?php endif;?>
<?php if(isset($entities['news']) && is_array($entities['news'])):?>
    <div class="container-header-auto-complete">
        <div class="block-auto-complete-header">Новости</div>
    </div>
    <div class="container-body-auto-complete main-pjax">
        <?php foreach ($entities['news'] as $item):?>
            <a class="a-auto-complete" href="/<?=$item['url_name'].'-n'.$item['id']?>">
                <?=$item['header']?>
            </a>
        <?php endforeach;?>
    </div>
<?php endif;?>
<?php if(isset($entities['discount']) && is_array($entities['discount'])):?>
    <div class="container-header-auto-complete">
        <div class="block-auto-complete-header">Скидки</div>
    </div>
    <div class="container-body-auto-complete main-pjax">
        <?php foreach ($entities['discount'] as $item):?>
            <a class="a-auto-complete" href="/<?=$item['url_name'].'-d'.$item['id']?>">
                <?=$item['header']?>
            </a>
        <?php endforeach;?>
    </div>
<?php endif;?>
