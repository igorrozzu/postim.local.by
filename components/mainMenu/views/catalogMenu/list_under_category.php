<?php
use yii\web;

$url_city=Yii::$app->city->Selected_city['url_name'];
if($url_city)$url_city='/'.$url_city;

echo '<li class="catalog-list-item"><a href="'.$category['url_name'].'>'.'Все'.'</a></li>';
foreach ($category->underCategory as $item){
    echo '<li class="catalog-list-item"><a href="'.$url_city.'/'.$item['url_name'].'">'.$item["name"].'</a></li>';
}
?>