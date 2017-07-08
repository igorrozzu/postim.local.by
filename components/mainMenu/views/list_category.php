<?php if (isset($category['name']) && isset($category['underCategory']) && count($category['underCategory']) != 0): ?>

    <li class="menu-category-list">
        <div class="category-list-title">
            <?php
            echo $category['name']
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