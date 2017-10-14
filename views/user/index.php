<?php
use yii\web\View;
use yii\widgets\Pjax;

$experience = $user->userInfo->getExperienceInfo();
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
                        <?=$user->userInfo->virtual_money;?> <span class="user-profile-points-text" style="margin-right: 5px;">руб</span>
                        и 15.30<span class="user-profile-points-text">мега-руб</span>
                    </div>
                    <?php endif;?>
                </div>
                <div class="container-progress-level">
                    <div class="progress-level-bar">
                        <div class="progress-complete" style="width: <?=$experience->persent?>%;"></div>
                    </div>
                </div>
                <div class="progress-level-text"><?=$user->userInfo->exp_points;?> опыта,
                    до следующего уровня нужно ещё <?=$experience->needExpForNextLevel?></div>
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
            <div class="block-photos-text"><?=$profilePhotoCount?> фотографий</div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => false,
    'id' => 'feeds-of-user',
    'linkSelector' => '.feeds-btn-bar a',
    'formSelector' => false,
]);
echo $feedReviews;
?>
<script>
    $(document).ready(function() {
        post.photos.setUserId(<?=$user->id?>);
        <?php if (isset($photoId)) :?>
        post.photos.initSliderByPhotoId('<?=$photoId?>', 'profile');
        <?php endif;?>
        $('.photo-header').mCustomScrollbar({axis: "x",scrollInertia: 50, scrollbarPosition: "outside"});
        $(".photo-wrap").swipe({
            swipeRight: function(event, direction) {
                post.photos.prevPhoto();
            },
            swipeLeft: function(event, direction) {
                post.photos.nextPhoto();
            }
        });
    })
</script>
<?php
Pjax::end();
?>
<div class="container-blackout-photo-popup"></div>
<div class="photo-popup">
    <div class="close-photo-popup"></div>
    <div class="photo-left-arrow"><div></div></div>
    <div class="photo-popup-content">
        <div class="photo-info">
            <div class="photo-header" >
                <a href="#">Title</a>
            </div>
        </div>
        <div class="photo-wrap">
            <div class="pre-photo pre-popup-photo"></div>
            <img class="photo-popup-item">
            <div class="next-photo next-popup-photo"></div>
        </div>
    </div>

    <div class="photo-right-arrow"><div></div></div>
    <ul class="wrap-photo-info">
        <li class="complain-gallery-text">Пожаловаться</li>
        <li class="photo-source" style="display: none;">
            <a href="#" target="_blank"><span>Источник</span></a>
        </li>
    </ul>

    <div class="gallery-counter"><span>1</span> из <?=$profilePhotoCount?></div>
</div>
<div style="margin-bottom:30px;"></div>

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