<?php
use app\components\user\ExperienceCalc;
use app\widgets\photoSlider\PhotoSlider;
use yii\widgets\Pjax;
$this->title = $user->name . ' ' . $user->surname.' на Postim.by';
$experience = ExperienceCalc::getExperienceInfo($user->userInfo->level, $user->userInfo->exp_points);
?>

<div class="margin-top60"></div>
<div class="block-profile-user" data-user-id="<?=$user->id?>">
    <div class="block-content">
        <div class="container-profile">
            <div class="profile-user-avatar">
                <img src="<?= $user->getPhoto();?>">
            </div>
            <div class="container-profile-info">
                <div class="name-user">
                    <?=$user->name . ' ' . $user->surname;?>
                </div>
                <div class="info-achievement">
                    <div class="user-level-profile"><?=$user->userInfo->level;?> <span class="user-profile-points-text">уровень</span></div>
                    <?php if(Yii::$app->user->id === $user->id): ?>
                        <div class="user-profile-points">
                            <?php if (isset($user->isOwner)): ?>
                                <?=$user->userInfo->virtual_money;?>
                                <span class="user-profile-points-text" style="margin-right: 5px;">руб</span>
                                и
                            <?php endif;?>
                            <?=$user->userInfo->mega_money?>
                            <span class="user-profile-points-text">мега-руб</span>
                        </div>
                    <?php endif;?>
                </div>
                <div class="container-progress-level">
                    <div class="progress-level-bar">
                        <div class="progress-complete" style="width: <?=$experience->persent?>%;"></div>
                    </div>
                </div>

                <?php if(Yii::$app->user->id === $user->id): ?>
                    <div class="progress-level-text">
                        <?=$user->userInfo->exp_points;?> опыта,
                        до следующего уровня нужно ещё <?=$experience->needExpForNextLevel?>
                    </div>
                <?php endif;?>

                <div class="info-city">
                    <?php if (isset($user->city)):?>
                        <?=$user->city->name?>
                    <?php endif;?>
                </div>
                <div class="block-social-info">
                    <?php foreach($user->socialBindings ?? [] as $binding):?>
                        <?php if($binding->source === 'google') continue;?>
                        <a class="<?=$binding->source?>-icon"
                           href="<?=$binding->createSocialUrl()?>" target="_blank"></a>
                    <?php endforeach;?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="block-content">
    <?php if($profilePhotoCount > 0): ?>
    <div class="block-photos-container">
        <div class="block-photos" data-type="profile">
            <?php foreach ($profilePreviewPhoto as $index => $photo):?>
                <div class="photo n<?=$index+1?>" style="background-image: url('<?=$photo->getPhotoPath()?>')" data-sequence="<?=$index?>"></div>
            <?php endforeach;?>
            <?php for ($i = count($profilePreviewPhoto); $i < 4; $i++):?>
                <div class="photo-not-found n<?=$i+1?>"></div>
            <?php endfor;?>
        </div>
        <div class="block-photos-bottom">
            <div class="block-photos-text">Фотографии <?=$profilePhotoCount?></div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => false,
    'id' => 'feeds-of-user',
    'linkSelector' => '#feeds-of-user .feeds-btn-bar a',
    'formSelector' => false,
]);
echo $feedReviews;
?>
<div style="margin-bottom:30px;"></div>
<?php
Pjax::end();

echo PhotoSlider::widget([
    'settings' => [
        'photoCount' => $profilePhotoCount,
    ],
]);
?>
<script>
    $(document).ready(function() {
        post.photos.setUserId(<?=$user->id?>);
        post.photos.setAllPhotoCount(<?=$profilePhotoCount?>);
        post.photos.isChangeTitleInSlider(true);
        <?php if (isset($initPhotoSliderParams['photoId'])) :?>
            post.photos.initPhotoSlider({
                photoId: '<?=$initPhotoSliderParams['photoId']?>',
                reviewId: <?=$initPhotoSliderParams['reviewId'] ?? 'null'?>,
                type: '<?=$initPhotoSliderParams['reviewId'] ? 'review' : 'profile'?>'
            });
        <?php endif;?>
    })
</script>
<?php
	$redirectMessage = Yii::$app->session->getFlash('redirect_after_add'.Yii::$app->user->getId());
	if($redirectMessage){
		$js = <<<js
    $(document).ready(function() {
        switch ('$redirectMessage'){
        	case 'place':{
        		var btn$ = $(".menu-btns-card.feeds-btn-bar a:eq(1)"),click = btn$.click.bind(btn$);
                setTimeout(click, 100);
                $().toastmessage('showToast', {text: 'Место добавлено',stayTime:5000,type:'success'});
        		
        	}break;
        	case 'place_edit':{
        		var btn$ = $(".menu-btns-card.feeds-btn-bar a:eq(1)"),click = btn$.click.bind(btn$);
                setTimeout(click, 100);
                $().toastmessage('showToast', {text: 'Место отредактировано',stayTime:5000,type:'success'});
        		
        	}break;
        	case 'moderation':{
        		var btn$ = $(".menu-btns-card.feeds-btn-bar a:eq(2)"),click = btn$.click.bind(btn$);
                setTimeout(click, 100);
        		$().toastmessage('showToast', {text: 'Спасибо, что помогаете! Ваше место будет рассмотрена модераторами и добавлено на сайт. Или нет',stayTime:5000,type:'success'});
        		
        	}break;
        	case 'moderation_edit':{
        		var btn$ = $(".menu-btns-card.feeds-btn-bar a:eq(2)"),click = btn$.click.bind(btn$);
                setTimeout(click, 100);
        		$().toastmessage('showToast', {text: 'Спасибо, что помогаете! Ваша редакция будет рассмотрена модераторами и добавлена на сайт. Или нет',stayTime:5000,type:'success'});
        		
        	}break;
        }
    })
js;
		echo '<script>'.$js.'</script>';
	}
?>