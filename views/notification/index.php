<?php
use yii\helpers\Url;

$data = $dataProvider->getModels();
$currentPage = $dataProvider->pagination->getPage() + 1;
$pageCount = $dataProvider->pagination->getPageCount();
?>
<?php foreach ($data as $item):?>
<div class="notif-item
    <?php if(!$item->isRead())
        echo 'notif-not-read'?>">

    <?php if(isset($item->sender)): ?>
    <a href="<?=Url::to(['user/index', 'id' => $item->sender->id])?>">
        <img src="<?=$item->sender->getPhoto();?>" class="user-icon"></a>
    <div class="user-info">
        <a class="notif-username" href="<?=Url::to(['user/index', 'id' => $item->sender->id])?>">
            <?=$item->sender->name . ' ' . $item->sender->surname;?>
        </a>
    <?php else:?>
    <img src="#" class="user-icon">
    <div class="user-info">
        <p class="notif-username">Postim.by</p>
    <?php endif;?>

        <div class="notif-date-time">
            <?=Yii::$app->formatter->printDate($item->date + Yii::$app->user->getTimezoneInSeconds())?>
        </div>
    </div>
    <div class="notif-text"><?=json_decode($item->message)->data;?>
        <div class="notif-date-time hidden-date">
            <?=Yii::$app->formatter->printDate($item->date + Yii::$app->user->getTimezoneInSeconds())?>
        </div>
    </div>
</div>
<?php endforeach;?>

<?php if ($currentPage < $pageCount): ?>
<div class="replace-notif-block" href="<?=$dataProvider->pagination->createUrl($currentPage)?>">
    <div class="bottom-btn" style="position: relative;">
        <span>Показать больше уведомлений</span>
    </div>
</div>
<?php endif;?>
