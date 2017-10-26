<?php
use yii\web\View;
?>
<div class="main-menu main-pjax">
    <div class="header-menu">
        <div class="header-menu-title">Меню</div>
        <div class="left-arrow close-main-menu"></div>
    </div>
    <div class="menu-content">
        <ul class="menu-category">
            <?php foreach ($data as $item):?>
                <?php
                    echo  $this->render('list_category',['item'=>$item]);
                ?>
            <?php endforeach;?>
        </ul>

    </div>
</div>