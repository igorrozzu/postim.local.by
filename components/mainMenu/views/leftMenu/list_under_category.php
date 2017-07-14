<?php
use yii\web;

$url_city=Yii::$app->city->Selected_city['url_name'];
if($url_city)$url_city='/'.$url_city;
?>
<li class="menu-category-item"><a href="<?= $url_city."/".$category["url_name"];?>">Все</a></li>
<?php foreach ($category->underCategory as $item){
 echo  '<li class="menu-category-item"><a href="'.$url_city."/".$item["url_name"].'">'.$item["name"].'</a></li>';
}
?>


