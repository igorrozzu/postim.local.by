<?php if($item['under']??false):?>
<li class="menu-category-list">
    <div class="category-list-title <?=$item['icon']?>" data-category_name="<?=$item['name']?>">
        <a><?=$item['name']?></a>
        <span class="open-list-btn"></span>
    </div>
    <ul class="menu-category-items">
        <?php
            foreach ($item['under'] as $under_item){
                echo $this->render('list_under_category',['under_item'=>$under_item]);
            }
        ?>
    </ul>
</li>
<?php else:?>
    <li class="menu-category-list" id="<?=$item['id']?>">
        <div class="news-list-title <?=$item['icon']?>">
            <a href="<?=$item['url']?>"><?=$item['name']?></a>
        </div>
    </li>

<?php endif;?>
