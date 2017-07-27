<?php
use app\components\cardsReviewsWidget\CardsReviewsWidget;
use yii\helpers\Url;
use yii\widgets\Pjax;
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
                    <div class="progress-level-bar"><div class="progress-complete"></div></div>
                </div>
                <div class="progress-level-text">36 опыта, до следующего уровня нужно ещё 4</div>
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
    <div class="block-photos-container">
        <div class="block-photos">
            <div class="photo n1" style="background-image: url('testP.png')"></div>
            <div class="photo n2" style="background-image: url('testP.png')"></div>
            <div class="photo n3" style="background-image: url('testP.png')"></div>
            <div class="photo n4" style="background-image: url('testP.png')"></div>
        </div>
        <div class="block-photos-bottom">
            <div class="block-photos-text">4805 фотографий</div>
        </div>
    </div>
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
Pjax::end();?>
<div style="margin-bottom:30px;"></div>