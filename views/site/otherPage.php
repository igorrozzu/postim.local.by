<?php

$this->title = $page['title'];
$this->registerMetaTag([
    'name' => 'description',
    'content' => $page['description'],
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content' => $page['key_word'],
]);

?>

<div class="margin-top60"></div>
<div class="block-content">
    <div class="container-columns">
        <div class="__first-column">
            <h1 style="margin-top: 35px" class="h1-v"><?= $page['h1'] ?></h1>
            <div style="margin-top: 30px;margin-bottom: 60px" class="container-post">
                <div class="container-content-post">
                    <?= $page['description_text'] ?>
                </div>
            </div>
        </div>
        <div class="__second-column">
            <?= \app\components\rightMenu\RightMenuWidget::widget() ?>
        </div>
    </div>


</div>
