<?php
if ($breadcrumbParams):

    $breadcrumb = \yii\helpers\ArrayHelper::map($breadcrumbParams, 'name', 'url_name');
    $i = 0;
    $count = count($breadcrumbParams);
    ?>
    <div class="bread-crumb">

        <?php foreach ($breadcrumb as $name => $url_name): ?>
            <?php if ($url_name != end($breadcrumb)): ?>
                <div <?= $breadcrumbParams[$i]['pjax'] ? $breadcrumbParams[$i]['pjax'] : '' ?> typeof="v:Breadcrumb">
                    <a href="<?= $url_name ?>" <?= $i == ($count - 2) ? 'class="pre"' : '' ?> rel="v:url"
                       property="v:title"><?= $name ?></a>
                </div>
                <span class="separator"></span>

            <?php else: ?>
                <div typeof="v:Breadcrumb">
                    <a style="display: none" href="<?= $url_name ?>" rel="v:url" property="v:title"><?= $name ?></a>
                    <p><?= $name ?></p>
                </div>

            <?php endif; ?>
            <?php $i++; ?>
        <?php endforeach; ?>

    </div>

<?php endif; ?>