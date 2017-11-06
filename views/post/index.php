<?php
use app\components\breadCrumb\BreadCrumb;
use \app\components\Helper;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

if(!$post->title){
    $post->title = $post->data.', '.
        mb_strtolower(Yii::t('app/singular',$post->onlyOnceCategories[0]->name)).' в '.
        Yii::t('app/locativus',$post->city->name).', '.
        $post->address.': адрес, телефоны и карта проезда';
}

if(!$post->description){
    $post->description = Yii::t('app/singular',$post->onlyOnceCategories[0]->name).' '.
        $post->data.' в '.
        Yii::t('app/locativus',$post->city->name).', '.
        $post->address.'. Адрес, телефоны и время работы — удобный поиск на карте Postim.by!';
}

if(!$post->key_word){
    $post->key_word = mb_strtolower(Yii::t('app/singular',$post->onlyOnceCategories[0]->name)).' '.
        $post->data.' '.
        $post->city->name;
}


$this->title = $post['title'];
$this->registerMetaTag([
    'name' => 'description',
    'content' => $post['description']
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content'=> $post['key_word']
]);
?>
<div class="margin-top60"></div>
<div id="map_block" class="block-map preload-map">
    <div class="btns-map">
        <div class="action-map" title="Открыть карту"></div>
        <div class="find-me" title="Найти меня"></div>
        <div class="zoom-plus"></div>
        <div class="zoom-minus"></div>
    </div>
    <div id="map" style="display: none"></div>
</div>

<?php
$js = <<<js
    $(document).ready(function() {
      map.setIdPlacesOnMap("$keyForMap");
    });
js;
echo "<script>$js</script>";
?>

<?php
Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => true,
    'id' => 'post-feeds',
    'linkSelector' => '#post-feeds .menu-btns-card a',
    'formSelector' => false,
])
?>
<div class="block-content">
    <?=BreadCrumb::widget(['breadcrumbParams'=>$breadcrumbParams])?>
    <h1 class="h1-v"><?=$post->data?></h1>
    <div class="block-info-reviewsAndfavorites" data-item-id="<?=$post->id?>" data-type="post">
        <div class="rating-b bg-r<?=$post['rating']?>" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
            <?=$post['rating']?>
            <meta itemprop="ratingCount" content="<?=$post->count_reviews?>">
            <meta itemprop="ratingValue" content="<?=$post['rating']?>">
            <meta itemprop="worstRating" content="1">
            <meta itemprop="bestRating" content="5">
        </div>
        <div class="count-reviews-text"><?=$post->count_reviews?> отзывов</div>
        <div class="add-favorite <?=$post['is_like']?'active':''?>"><?=$post->count_favorites?></div>
    </div>
</div>
<div class="block-flex-white">
    <div class="block-content">
        <div class="menu-btns-card">

            <a href="<?=Url::to(['post/index', 'url' => $post['url_name'], 'id' => $post['id']])?>" >
                <div class="btn2-menu active"><span class="under-line">Информация</span></div>
            </a>
            <a href="<?=Url::to(['post/gallery', 'name' => $post['url_name'], 'postId' => $post['id']])?>">
                <div class="btn2-menu "><span class="under-line">Фотографии <?=$photoCount?></span></div>
            </a>
            <a href="<?=Url::to(['post/reviews', 'name' => $post['url_name'], 'postId' => $post['id']])?>">
                <div class="btn2-menu"><span class="under-line">Отзывы <?=$post['count_reviews']?></span></div>
            </a>
        </div>
    </div>
</div>
<div class="block-content">
    <div class="block-photos-container">
        <div class="block-photos" data-type="all">
            <?php foreach ($previewPhoto as $index => $photo):?>
            <div class="photo n<?=$index+1?>" style="background-image: url('<?=$photo->getPhotoPath()?>')" data-sequence="<?=$index?>"></div>
            <?php endforeach;?>
            <?php for ($i = count($previewPhoto); $i < 4; $i++):?>
                <div class="photo-not-found n<?=$i+1?>"></div>
            <?php endfor;?>
        </div>
        <div class="block-photos-bottom">
            <div class="block-photos-text"><?=$photoCount?> фотографий</div>
            <label class="btn-add-photo photo-upload-sign" for="post-photos">Добавить фото</label>
            <input type="file" name="post-photos" id="post-photos" style="display: none;" multiple
                   accept="image/*,image/jpeg,image/gif,image/png" data-id="<?=$post->id?>">
        </div>
    </div>
    <div class="block-content-between cust">
        <h2 class="h2-v">Информация</h2>
        <p class="text p-text main-pjax">
            Нашли неточность или ошибку,&nbsp;<a class="href-edit" href="/edit/<?=$post['id']?>">исправьте&nbsp;или&nbsp;дополните&nbsp;информацию</a>
        </p>
    </div>
    <div class="block-info-card">
        <?php if($post['address']):?>
            <div class="info-row">
                <div class="left-block-f1">
                    <div class="address-card"><span>Адрес</span></div>
                    <div class="block-inside">
                        <p class="info-card-text"><?=$post->city['name'].', '.$post['address']?></p>
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

        <?php if($post->metro):?>
            <div class="info-row">
                <div class="left-block-f1">
                    <div class="metro-card">Метро</div>
                    <div class="block-inside">
                        <p class="info-card-text"><?=$post->metro?></p>
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
                    <div class="web-site-card">Веб-сайт</div>
                    <div class="block-inside">
                        <p class="info-card-text">
                            <a target="_blank" rel="nofollow noopener" href="<?=$post->info['web_site']?>"><?=Helper::getDomainNameByUrl($post->info['web_site'])?></a>
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
                    <div class="block-inside social-info">
                        <div class="block-social-info">
                            <?php foreach ($post->info['social_networks'] as $key => $social_network):?>
                                <?php if(is_array($social_network)):?>
                                    <?php foreach ($social_network as $keyItem => $valueItem):?>
                                        <a target="_blank" rel="nofollow noopener" href="<?=$valueItem?>" class="<?=$keyItem?>-icon"></a>
                                    <?php endforeach;?>
								<?php else:?>
                                    <a target="_blank" rel="nofollow noopener" href="<?=$social_network?>" class="<?=$key?>-icon"></a>
                                <?php endif;?>
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
                                    <div class="sh-time-start"><?=Yii::$app->formatter->asTime($workingHour['time_start'], 'HH:mm')?></div>
                                    <div class="sh-time-finish"><?=Yii::$app->formatter->asTime($workingHour['time_finish'], 'HH:mm')?></div>
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
            <?php Helper::getFeature($post->getFeatures())?>
        <?php if($post->requisites):?>
        <div class="info-row">
            <div class="left-block-f">
                <div class="title-info-card">Реквизиты</div>
                <div class="block-inside">
                    <p class="info-card-text"><?=$post->requisites?></p>
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
                <div class="block-inside user-editor">
                    <div class="container-user-editor">
                        <ul>
							<?php if ($post->info && is_array($post->info->editors_users)): ?>
								<?php foreach ($post->info->editors_users as $editor): ?>
                                    <li>
                                        <a href="/id<?= $editor->id ?>">
                                            <img src="<?= $editor->getPhoto() ?>">
                                            <span><?=$editor->name.' '.$editor->surname?></span>
                                        </a>
                                    </li>
								<?php endforeach; ?>
							<?php endif; ?>
                        </ul>
                    </div>
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

    <?=$this->render('__reviews',['reviewsDataProvider'=>$reviewsDataProvider,'post_id'=>$post->id])?>
    <?php if(!$post->has_send_bs):?>
    <div class="block-info-for-owner" data-post_id="<?=$post->id?>">
        <p>Вы владелец этого места? Зарегистрируйте бесплатный бизнес-аккаунт и отвечайте на отзывы от имени компании.</p>
    </div>
    <?php endif;?>
    <div class="margin-top60"></div>
</div>
<?php if(Yii::$app->request->get('review_id',false)):?>
    <script>
        $(document).ready(function() {
            reviews.scrollToFirstReviews();
        });
    </script>
<?php endif;?>
<script>
    $(document).ready(function() {
        post.info.init();
        post.photos.setLoadTime(<?=time()?>);
        post.photos.setPostId(<?=$post->id?>);
        post.photos.setAllPhotoCount(<?=$photoCount?>);
        post.photos.resetContainer();
        <?php if (isset($initPhotoSliderParams['photoId'])) :?>
            post.photos.initPhotoSlider({
                photoId: '<?=$initPhotoSliderParams['photoId']?>',
                reviewId: <?=$initPhotoSliderParams['reviewId'] ?? 'null'?>,
                type: '<?=$initPhotoSliderParams['reviewId'] ? 'review' : 'all'?>'
            });
        <?php endif;?>
        main.initCustomScrollBar($('.photo-header'),{axis: "x",scrollInertia: 50, scrollbarPosition: "outside"})
        $(".photo-wrap").swipe({
            swipeRight: function(event, direction) {
                post.photos.prevPhoto();
            },
            swipeLeft: function(event, direction) {
                post.photos.nextPhoto();
            }
        });
		menu_control.fireMethodClose();
        search.clear();
    })
</script>

<input style="display: none" class="photo-add-review" name="photo-add-review" type="file" multiple
accept="image/*,image/jpeg,image/gif,image/png">

<div class="container-blackout-photo-popup"></div>
<div class="photo-popup">
    <div class="close-photo-popup"></div>
    <div class="photo-left-arrow"><div></div></div>
    <div class="photo-popup-content">
        <div class="photo-info">
            <div class="photo-header" >
                <a href="<?=$post['url_name']?>-p<?=$post['id']?>"><?=$post->data?></a>
            </div>
        </div>
        <div class="photo-wrap">
            <img class="photo-popup-item">
        </div>
    </div>
    <div class="photo-right-arrow"><div></div></div>
    <ul class="wrap-photo-info">
        <li class="complain-gallery-text">Пожаловаться</li>
        <li class="photo-source" style="display: none;">
            <a href="#" rel="nofollow noopener" target="_blank"><span>Источник</span></a>
        </li>
    </ul>
    <div class="gallery-counter">
        <span id="start-photo-counter">1</span> из
        <span id="end-photo-counter"><?=$photoCount?></span>
    </div>
</div>
<?php
Pjax::end();
?>
