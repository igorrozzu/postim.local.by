<?php
if($breadcrumbParams):

    $breadcrumb = \yii\helpers\ArrayHelper::map($breadcrumbParams,'name','url_name');
    $i=0;
?>
    <div class="bread-crumb">

        <?php foreach ($breadcrumb as $name=>$url_name):?>
            <?php if ($url_name != end($breadcrumb)): ?>
                <div <?=$breadcrumbParams[$i++]['pjax']?'class="main-pjax"':''?> typeof="v:Breadcrumb">
                    <a href="<?=$url_name?>" rel="v:url" property="v:title"><?=$name?></a>
                </div>
                <span class="separator"></span>

            <?php else: ?>
                <div typeof="v:Breadcrumb">
                    <a style="display: none" href="<?=$url_name?>" rel="v:url" property="v:title"><?=$name?></a>
                    <p><?=$name?></p>
                </div>

            <?php endif; ?>
        <?php endforeach;?>

    </div>

<?php endif;?>