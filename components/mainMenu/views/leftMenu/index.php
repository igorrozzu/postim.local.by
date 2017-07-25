<?php
use yii\web\View;
?>
<!--левое меню-->
<div class="main-menu main-pjax">
    <div class="header-menu">
        <div class="header-menu-title">Меню</div>
        <div class="header-menu-title btn-select-city"><?=\Yii::$app->city->Selected_city['name']?></div>
        <div class="left-arrow close-main-menu"></div>
    </div>
    <div class="menu-content">
        <ul class="menu-category">
            <?php
                foreach ($dataprovider as $item){
                    echo  $this->render('list_category',['category'=>$item]);
                }
            ?>
            <li class="menu-category-list">
                <div class="news-list-title">
                    <a>Новости</a>
                </div>
            </li>
        </ul>
    </div>
</div>
<!--левое меню end-->
<?php