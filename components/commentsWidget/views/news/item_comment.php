<?php
$model = $dataprovider->getModels();
foreach ($model as $item):
?>
<div class="container-comment main" data-comment_id="<?=$item->id?>" id="comment-<?=$item->id?>">
    <div class="profile-commentator main-pjax">
        <a href="/id<?=$item->user->id?>"><img class="profile-icon-commentator" src="<?=$item->user->getPhoto()?>"></a>
    </div>
    <div class="comment-content">
        <div class="comment-content-header">
            <div class="content-between main-pjax">
                <a href="/id<?=$item->user->id?>" class="user-name"><?=$item->user->name?> <?=$item->user->surname?></a>
                <div class="user-level"><?=$item->user->userInfo->level?>
                    <noindex>
                        <span>&nbsp;уровень</span>
                    </noindex>
                </div>
            </div>
            <span class="comment-time"><?=Yii::$app->formatter->printDate($item->date)?></span>
        </div>
        <div class="comment-text">
            <?php
            if ($item->status == 0) {
                echo \yii\helpers\Html::encode($item->data);
            } else {
                echo '<p class="comment-status">'.\app\models\Comments::$status_map[$item->status].'</p>';
            }
            ?>
        </div>
        <div class="btns-comment">
            <div class="btn-comment btn-like <?=$item->is_like?'active':''?>"><?=$item->like?></div>
            <div class="btn-comment btn-comm reply">
                <noindex>Ответить</noindex>
            </div>
            <?php if(Yii::$app->user->isGuest || $item->user->id!=Yii::$app->user->id):?>
                <?php if(!$item->is_complaint):?>
                    <div class="btn-comment cplt">
                        <noindex>Пожаловаться</noindex>
                    </div>
                <?php endif;?>
            <?php elseif($item->status == 0):?>
                <div class="btn-comment delete">Удалить</div>
            <?php endif;?>
        </div>
    </div>
</div>
<?php
    if($item->underComments){
        echo $this->render('item_under_comments',[
            'underComments'=>$item->underComments,
        ]);
    }
?>
<?php endforeach; ?>

<?php if ($hrefNext = $dataprovider->pagination->getLinks()['next'] ?? false): ?>
    <div class="replace-block" id="news-comments">
        <div class="btn-show-more" data-selector_replace="#news-comments" data-href="<?=$hrefNext?>">Показать больше комментариев</div>
    </div>
<?php endif; ?>
