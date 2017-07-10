<li class="menu-category-item"><a href="/<?=$category['url_name'];?>">Все</a></li>
<?php foreach ($category->underCategory as $item){
 echo  "<li class='menu-category-item'><a href=" . "{$item['url_name']}'/'>{$item['name']}</a></li>";
}
?>


