<?php if (isset($category['name']) && isset($category['underCategory']) && count($category['underCategory']) != 0): ?>
<li class="catalog-category  bg-green" data-open=false>
    <div class="catalog-title">
        <div class="catalog-title-name "><?=$category['name']?></div>
        <div class="catalog-title-btn btn-down"></div>
    </div>
    <ul class="catalog-list" style="height: 0">
        <?php
            echo $this->render('list_under_category', [
                'category' => $category
            ]);
        ?>
    </ul>
</li>
<?php endif;?>