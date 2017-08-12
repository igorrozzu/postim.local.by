<?php
use \app\components\Helper;
?>
<div class="margin-top60"></div>
<div id="map_block" class="block-map"></div>
<div class="block-content">
    <?=\app\components\breadCrumb\BreadCrumb::widget(['breadcrumbParams'=>$breadcrumbParams])?>
    <h1 class="h1-v"><?=$post->data?></h1>
    <div class="block-info-reviewsAndfavorites" data-item-id="<?=$post->id?>" data-type="post">
        <div class="rating-b bg-r<?=$post['rating']?>"><?=$post['rating']?></div>
        <div class="count-reviews-text"><?=$post->count_reviews?> отзывов</div>
        <div class="add-favorite <?=$post['is_like']?'active':''?>"><?=$post->count_favorites?></div>
    </div>
</div>
<div class="block-flex-white">
    <div class="block-content">
        <div class="menu-btns-card">
            <div class="btn2-menu active">Информация</div>
            <div class="btn2-menu">Фотографии 48</div>
            <div class="btn2-menu">Отзывы</div>
            <div class="btn2-menu">Скидки</div>
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
            <div class="btn-add-photo">Добавить фото</div>
        </div>
    </div>
    <div class="block-content-between">
        <h2 class="h2-v">Информация</h2>
        <p class="text p-text">
            Нашли неточность или ошибку, <a class="href-edit" href="#">исправьте&nbsp;или&nbsp;дополните&nbsp;информацию.</a>
        </p>
    </div>
    <div class="block-info-card">
        <?php if($post['address']):?>
            <div class="info-row">
                <div class="left-block-f1">
                    <div class="address-card">Адрес</div>
                    <div class="block-inside">
                        <p class="info-card-text"><?=$post['address']?></p>
                        <?php if($post['additional_address']):?>
                            <div class="dop-info"><?=$post['additional_address']?></div>
                        <?php endif;?>
                    </div>
                </div>
                <div class="right-block-f">
                    <div class="btn-info-card"></div>
                </div>
            </div>
        <?php endif;?>
        <?php if($post->info['phones']):?>

            <div class="info-row">
                <div class="left-block-f1">
                    <div class="phone-card"><span><?=substr($post->info['phones'][0],0,8)?>...</span></div>
                    <div class="block-inside">
                        <p class="info-card-text">
                            Показать телефон
                        </p>
                        <ul class="lists-phones">
                            <?php foreach ($post->info['phones'] as $phone): ?>
                                <li><?= $phone ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="right-block-f">
                    <div class="btn-info-card"></div>
                </div>
            </div>
        <?php endif;?>
        <?php if($post->info['web_site']):?>
            <div class="info-row">
                <div class="left-block-f1">
                    <div class="web-site-card">Веб сайт</div>
                    <div class="block-inside">
                        <p class="info-card-text">
                            <a href="<?=$post->info['web_site']?>"><?=Helper::getDomainNameByUrl($post->info['web_site'])?></a>
                        </p>
                    </div>
                </div>
                <div class="right-block-f">
                    <div class="btn-info-card"></div>
                </div>
            </div>
        <?php endif;?>
        <?php if($post->info['social_networks']):?>
            <div class="info-row">
                <div class="left-block-f">
                    <div class="title-info-card">Социальные&nbsp;сети</div>
                    <div class="block-inside">
                        <div class="block-social-info">
                            <?php foreach ($post->info['social_networks'] as $key => $social_network):?>
                                <a href="<?=$social_network?>" class="<?=$key?>-icon"></a>
                            <?php endforeach;?>
                        </div>
                    </div>
                </div>
                <div class="right-block-f">
                    <div class="btn-info-card"></div>
                </div>
            </div>
        <?php endif;?>
        <div class="info-row">
            <div class="left-block-f">
                <div class="title-info-card">Режим&nbsp;работы</div>
                <div class="block-inside">
                    <div class="block-time-work">
                        <?php if($post->is_open):?>
                            <div class="open"> Открыто <?=$post->timeOpenOrClosed?></div>
                        <?php else:?>
                            <div class="close"> Закрыто <?=$post->timeOpenOrClosed?></div>
                        <?php endif;?>
                        <?php if($post->is_open || $post->timeOpenOrClosed!==null):?>
                        <div class="block-schedules">
                            <?php foreach ($post->workingHours as $workingHour):?>
                                <div class="sh-day">
                                    <div class="sh-title-day"><?=Helper::getShortNameDayById($workingHour['day_type'])?></div>
                                    <div class="sh-time-start"><?=Yii::$app->formatter->asTime($workingHour['time_start'], 'short')?></div>
                                    <div class="sh-time-finish"><?=Yii::$app->formatter->asTime($workingHour['time_finish'], 'short')?></div>
                                </div>
                            <?php endforeach;?>
                        </div>
                        <?php endif;?>
                    </div>
                </div>
            </div>
            <div class="right-block-f">
                <div class="btn-info-card"></div>
            </div>
        </div>
        <?php if(is_array($post->info['features'])):?>
            <div class="info-row">
                <div class="left-block-f">
                    <div class="title-info-card">Особенности</div>
                    <div class="block-inside">
                        <ul class="lists-features">
                            <?php foreach ($post->info['features'] as $key=>$feature):?>
                                <li class="lists-feature"><?=Helper::getFeature($key,$feature)?></li>
                            <?php endforeach;?>
                        </ul>
                    </div>
                </div>
                <div class="right-block-f">
                    <div class="btn-info-card"></div>
                </div>
            </div>
        <?php endif;?>

        <div class="info-row">
            <div class="left-block-f">
                <div class="title-info-card">Редакторы</div>
                <div class="block-inside">
                    <p class="info-card-text2">
                        ПостимБай
                    </p>
                </div>
            </div>
            <div class="right-block-f">
                <div class="btn-info-card"></div>
            </div>
        </div>
    </div>
    <?php if($post->info && $post->info->article):?>
    <h2 class="h2-c">Описание</h2>
    <div class="block-description-card">
        <?=$post->info->article?>
    </div>
    <?php endif;?>
    <div class="block-content-between">
        <div class="block-social-share">
            <div class="social-btn-share goodshare" data-type="vk"><p>Поделиться</p> <span data-counter="vk">0</span></div>
            <div class="social-btn-share goodshare" data-type="fb"><p>Share</p><span data-counter="fb">0</span></div>
            <div class="social-btn-share goodshare" data-type="tw"><p>Твитнуть</p></div>
            <div class="social-btn-share goodshare" data-type="ok"><span data-counter="ok">0</span></div>
        </div>
        <div class="block-count-views">
            <div class="elem-count-views"><?=$post->totalView['count']?></div>
        </div>
    </div>


    <h2 class="h2-c">
        Отзывы <span>38</span>
    </h2>
    <div class="block-write-reviews">
        <div class="profile-user-reviews">
            <img class="profile-icon60x60" src="img/default-profile-icon.png">
            Гость
        </div>
        <div class="large-wide-button"><p>Написать новый отзыв</p></div>
    </div>
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
    <div class="block-info-for-owner"><p>Вы владелец этого места? Зарегистрируйте бесплатный бизнес-аккаунт и отвечайте на отзывы от имени компании.<br>
            Рекламируйтесь бесплатно размещая свои скидки и акции на сайте Posim.by</p>
    </div>
    <div class="block-add-review">
        <div class="add-review-profile">
            <img class="profile-icon60x60" src="img/default-profile-icon.png">
            <p class="profile-name">Гость</p>
        </div>
        <div class="add-review-label">Поставте вашу оценку</div>
        <div class="container-evaluations">
            <div class="evaluation">1</div>
            <div class="evaluation">2</div>
            <div class="evaluation">3</div>
            <div class="evaluation">4</div>
            <div class="evaluation">5</div>
        </div>
        <div class="add-review-label">Напишите отзыв</div>
        <div class="block-textarea-review">
            <textarea placeholder="Пожалуйста, аргументируйте свою оценку. Напишите не менее 100 символов." class="textarea-review"></textarea>
            <div class="container-add-photo" style="background-image: url('testP.png')">
                <div class="close-add-photo"></div>
            </div>
            <div class="block-btns-textarea-review">
                <div class="btn-add-photo-review"><p>Добавить фото</p></div>
                <div class="btn-rule"><a href="#">Правила размещения отзывов</a></div>
            </div>
        </div>
        <div class="large-wide-button"><p>Опубликовать</p></div>
    </div>
</div>
<?php
$js = <<<js
    $(document).ready(function() {
        post.info.init();
    })
js;

echo "<script>$js</script>";

?>