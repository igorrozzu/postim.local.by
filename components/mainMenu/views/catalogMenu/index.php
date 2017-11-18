<div class="content2b main-pjax">
    <div class="nav-bar1">
        <ul class="catalog">
            <?php

            $colorMap = [
                0 => 'bg-blue',
                1 => 'bg-green',
                2 => 'bg-yellow',
                3 => 'bg-purple',
                4 => 'bg-red',
            ];
            $i=0;

            foreach ($dataprovider as $item){
                echo  $this->render('list_category',['category'=>$item,'color'=>$colorMap[$i]]);
                $i = isset($colorMap[++$i])?$i:0;
            }
            ?>
        </ul>
    </div>
</div>
<div class="clear-fix"></div>