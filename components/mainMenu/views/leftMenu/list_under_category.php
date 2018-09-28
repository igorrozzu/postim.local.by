<?php

use yii\web;

$url_city = Yii::$app->city->Selected_city['url_name'];
if ($url_city) {
    $url_city = '/' . $url_city;
}
?>
<li class="menu-category-item"><a href="<?= $url_city . "/" . $category["url_name"]; ?>">Все<span
                class="count-place"><?= $category->countPlace->count ?></span></a></li>
<?php foreach ($category->underCategorySort as $item) {
    if ($item->isRelationPopulated('countPlace') && $item->countPlace != null && $item->countPlace->count != 0) {
        echo '<li class="menu-category-item" data-under_category_name="' . $item["name"] . '"><a href="' . $url_city . "/" . $item["url_name"] . '">' . $item["name"] . '<span class="count-place">' . $item->countPlace->count . '</span></a></li>';
    }
}
?>

