<?php

use yii\helpers\Url;

$notifications = $dataProvider->getModels();
$currentPage = $dataProvider->pagination->getPage() + 1;
$pageCount = $dataProvider->pagination->getPageCount();
?>
<?php foreach ($notifications

as $item): ?>
<div class="notif-item
    <?php if (!$item->isShowed())
    echo 'notif-not-read' ?>">

    <?php if (isset($item->notification->sender)): ?>
    <a href="<?= Url::to(['user/index', 'id' => $item->notification->sender->id]) ?>">
        <img src="<?= $item->notification->sender->getPhoto(); ?>" class="user-icon"></a>
    <div class="user-info">
        <a class="notif-username" href="<?= Url::to(['user/index', 'id' => $item->notification->sender->id]) ?>">
            <?= $item->notification->sender->name . ' ' . $item->notification->sender->surname; ?>
        </a>
        <?php else: ?>
        <img src="/img/logo.jpg" class="user-icon">
        <div class="user-info">
            <p class="notif-system-username">Postim.by</p>
            <?php endif; ?>

            <div class="notif-date-time">
                <?= Yii::$app->formatter->printDate($item->notification->date + Yii::$app->user->getTimezoneInSeconds()) ?>
            </div>
        </div>
        <div class="notif-text"><?= json_decode($item->notification->message)->data; ?>
            <div class="notif-date-time hidden-date">
                <?= Yii::$app->formatter->printDate($item->notification->date + Yii::$app->user->getTimezoneInSeconds()) ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <?php if ($currentPage < $pageCount): ?>
        <div class="replace-notif-block" href="<?= $dataProvider->pagination->createUrl($currentPage) ?>">
            <div class="bottom-btn" style="position: relative;">
                <span>Показать больше уведомлений</span>
            </div>
        </div>
    <?php endif; ?>
