<?php
use \yii\helpers\Url;
$currentUrl = Url::base(true).'/'.Yii::$app->request->getPathInfo();

?>

    <div class="nav-right-menu">
        <ul class="right-menu-list">
            <?php if (count($list)): ?>
                <?php foreach ($list as $item): ?>
                    <li class="right-menu-item main-pjax">
                        <a <?=Url::base(true) . $item['url_name'] == $currentUrl?'class="active"':''?> href="<?= Url::base(true) . $item['url_name'] ?>"><?= $item['h1'] ?></a>
                    </li>
                <?php endforeach; ?>
                <li class="right-menu-item main-pjax">
                    <a <?=Url::base(true) . '/feedback' == $currentUrl?'class="active"':''?> href="<?= Url::base(true) . '/feedback' ?>">Обратная связь</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>



