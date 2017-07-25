<div class="margin-top60"></div>
<div class="block-profile-user">
    <div class="block-content">
        <div class="container-profile">
            <div class="profile-user-avatar">
                <img src="<?=$user->getPhoto();?>">
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
<div class="block-flex-white">
    <div class="block-content">
        <div class="menu-btns-card">
            <div class="btn2-menu">Отзывы</div>
            <div class="btn2-menu">Комментарии <?=$user->userInfo->total_comments;?></div>
            <div class="btn2-menu">Места <?=$user->userInfo->count_places_added;?></div>
            <div class="btn2-menu">На модерации <?=$user->userInfo->count_place_moderation;?></div>
        </div>
    </div>
</div>
<div class="block-content">
    <div class="block-reviews">
        <div class="review-header">
            <img class="profile-icon60x60" src="img/default-profile-icon.png">
            <div class="block-between">
                <div class="block-info-review">
                    <div class="block-info-review-user">
                        <p class="user-name">Василий Утконосов</p>
                        <span class="user-points">123</span>
                    </div>
                    <div class="date-time-review">21 февраля в 10:45</div>
                </div>
                <div class="block-btn-circle-share">
                    <div class="btn-circle-share"></div>
                    <div class="btn-circle-share"></div>
                    <div class="btn-circle-share"></div>
                    <div class="btn-circle-share"></div>
                    <div class="btn-circle-share open-share"></div>
                </div>
            </div>
        </div>
        <div class="block-review-content">
            <div class="rating-r bg-r4">4</div>
            <div class="review-text">Все очень вкусно, пришли, заказали роллы. (Очень вкусные, свежие, сочные) Принесли минут за 10, вежливый
                персонал. Может потому что будний день, все очень быстро... не знаю. Напитки мгновенно. Нас ничего не
                смутило. Попробуйте "жареное молоко" из десертов. Вкусно, ням-нам-ням были первый раз. По возможности
                заглянем ещё.</div>
        </div>
        <div class="review-photo" style="background-image: url('testP.png')">
            <div class="block-total-photo-review">12</div>
        </div>
        <div class="review-footer">
            <div class="review-footer-btn btn-like">2</div>
            <div class="review-footer-btn btn-comm">Ответить</div>
            <div class="review-footer-btn">Пожаловаться</div>
        </div>
    </div>
    <div class="review-show-more">
        <div class="btn-show-more">Показать больше отзывов</div>
    </div>
</div>

<div style="margin-bottom:30px;"></div>