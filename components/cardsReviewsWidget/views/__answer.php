<div class="official_answer">
<div class="container-comment">
	<div class="profile-commentator main-pjax">
		<a href="/id<?=$item->user->id?>"><img class="profile-icon-commentator" src="<?=$item->user->getPhoto()?>"></a>
	</div>
	<div class="comment-content">
		<div class="comment-content-header">
			<div class="content-between main-pjax">
				<a href="/id<?=$item->user->id?>" class="user-name"><?=$item->user->name?> <?=$item->user->surname?></a>
				<div class="official-sign">Официальный ответ</div>
				<div class="user-level"><?=$item->user->userInfo->level?> <span>&nbsp;уровень</span></div>
			</div>
			<span class="comment-time"><?=Yii::$app->formatter->printDate($item->date)?></span>
		</div>
		<div class="comment-text"><?=\yii\helpers\Html::encode($item->data);?></div>
	</div>
</div>
</div>