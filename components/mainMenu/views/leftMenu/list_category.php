<?php if (isset($category['name'])
    && isset($category['underCategory'])
    && count($category['underCategory']) != 0
    && $category->isRelationPopulated('countPlace')
    && $category->countPlace != null
    && $category->countPlace->count != 0
):
?>

    <li class="menu-category-list">
        <div class="category-list-title" data-category_name="<?=$category['name']?>">
            <?php
            echo '<a>'.$category['name'].'</a>';
            ?>
            <span class="open-list-btn"></span>
        </div>
        <ul class="menu-category-items">
            <?php
            echo $this->render('list_under_category', [
                'category' => $category
            ]);

            ?>
        </ul>
    </li>
<?php endif; ?>