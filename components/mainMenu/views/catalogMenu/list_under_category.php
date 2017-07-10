<?php foreach ($category->underCategory as $item){
    echo "<li class=\"catalog-list-item\"><a href='{$item['url_name']}'>{$item['name']}</a></li>";
}
?>