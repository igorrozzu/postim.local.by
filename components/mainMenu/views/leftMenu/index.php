<?php
$city = \Yii::$app->city->Selected_city;
?>

<div class="main-menu main-pjax">
    <div class="header-menu">
        <div class="header-menu-title">Меню</div>
        <div class="header-menu-title btn-select-city"><?= $city['name'] ?></div>
        <div class="left-arrow close-main-menu"></div>
    </div>
    <div class="menu-content">
        <ul class="menu-category">
            <?php
            foreach ($dataprovider as $item) {
                echo $this->render('list_category', ['category' => $item]);
            }
            ?>
            <li class="menu-category-list" id="btn-all-discounts">
                <div class="news-list-title">
                    <a href="<?= $city['url_name'] ? '/' . $city['url_name'] : '' ?>/skidki">Скидки</a>
                </div>
            </li>

            <li class="menu-category-list" id="btn-menu-news">
                <div class="news-list-title">
                    <a href="<?= $city['url_name'] ? '/' . $city['url_name'] : '' ?>/novosti">Новости</a>
                </div>
            </li>

            <li class="menu-category-list" id="btn-all-reviews">
                <div class="news-list-title">
                    <a href="<?= $city['url_name'] ? '/' . $city['url_name'] : '' ?>/otzyvy">Все отзывы</a>
                </div>
            </li>
        </ul>
    </div>
</div>