<?php if (isset($category['name'])
    && isset($category['underCategory'])
    && count($category['underCategory']) != 0
    && $category->isRelationPopulated('countPlace')
    && $category->countPlace != null
    && $category->countPlace->count != 0
): ?>
    <li class="catalog-category  <?= $color; ?>" data-open=false data-category_name="<?= $category['name'] ?>">
        <div class="catalog-title">
            <div class="catalog-title-name "><?= $category['name'] ?></div>
            <div class="catalog-title-btn btn-down"></div>
        </div>
        <ul class="catalog-list" style="height: 0">
            <?php
            echo $this->render('list_under_category', [
                'category' => $category,
            ]);
            ?>
        </ul>
    </li>
<?php endif; ?>