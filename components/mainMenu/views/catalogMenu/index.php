<div class="content2b">
    <div class="nav-bar1">
        <ul class="catalog">
            <?php
            foreach ($dataprovider as $item){
                echo  $this->render('list_category',['category'=>$item]);
            }
            ?>
        </ul>
    </div>
    <div class="right-block"></div>
</div>
<div class="clear-fix"></div>