<?php
use app\components\breadCrumb\BreadCrumb;
use \app\components\Helper;
use app\components\rightBlock\RightBlockWidget;
use app\widgets\cardsDiscounts\CardsDiscounts;
use app\widgets\cardsRecommendedPlace\CardsRecommendedPlace;
use app\widgets\photoSlider\PhotoSlider;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

if(!$post->title){
    $post->title = $post->data.', '.
        mb_strtolower(Yii::t('app/singular',$post->onlyOnceCategories[0]->name)).' в '.
        Yii::t('app/locativus',$post->city->name).
        ': отзывы, адрес, телефоны и карта проезда';
}

if(!$post->description){
    $post->description = Yii::t('app/singular',$post->onlyOnceCategories[0]->name).' '.
        $post->data.' в '.
        Yii::t('app/locativus',$post->city->name).', '.
        $post->address.'. Отзывы посетителей, адрес и время работы — удобный поиск на карте Postim.by!';
}

if(!$post->key_word){
    $post->key_word = $post->data.', '.
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

$paramsMetaTagsOg = [
    'og:locale'=>'ru_RU',
    'og:type'=>'article',
    'og:title'=>$post->data,
    'twitter:title'=>$post->data,
    'og:description'=>$this->title,
    'twitter:description'=>$this->title,
    'og:url'=>\yii\helpers\Url::to('',true),
    'og:site_name'=>'Postim.by',
    'twitter:site'=>'Postim.by',
];


if($previewPhoto[0]??false){
    $paramsMetaTagsOg['og:image'] = Yii::$app->params['site.hostName'].$previewPhoto[0]->getPhotoPath();
    $paramsMetaTagsOg['twitter:image:src'] = Yii::$app->params['site.hostName']. $previewPhoto[0]->getPhotoPath();
}else{
    $paramsMetaTagsOg['og:image'] = Yii::$app->params['site.hostName'].'/default_img.jpg';
    $paramsMetaTagsOg['twitter:image:src'] = Yii::$app->params['site.hostName']. '/default_img.jpg';
}

\app\components\MetaTagsSocialNetwork::initOg($this,$paramsMetaTagsOg);

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

<div itemscope itemtype="http://schema.org/LocalBusiness">
<div class="block-content">
    <?=BreadCrumb::widget(['breadcrumbParams'=>$breadcrumbParams])?>
    <?php
        $h1 = $post->data;

    if ( !in_array( $post->onlyOnceCategories[ 0 ]->name, [
        'Церкви, Монастыри и Костелы',
        'Достопримечательности',
        'Замки и дворцы',
        'Старинные усадьбы',
        'Заброшенные места',
        'Заказники',
        'Музеи',
        'Памятники',
        'Площади',
        'Улицы',
    ] ) )
        {
            $h1 = ucfirst(Yii::t('app/singular',$post->onlyOnceCategories[0]->name)) . ' ' . $h1;
        }
    ?>
    <h1 itemprop="name" class="h1-v"><?=$h1?></h1>

    <div class="block-info-reviewsAndfavorites" data-item-id="<?=$post->id?>" data-type="post">
        <div class="rating-b bg-r<?=$post['rating']?>" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
            <?=$post['rating']?>
            <meta itemprop="ratingCount" content="<?=$post->count_reviews?>">
            <meta itemprop="ratingValue" content="<?=$post['rating']?>">
            <meta itemprop="worstRating" content="1">
            <meta itemprop="bestRating" content="5">
        </div>
        <div class="count-reviews-text">
            <?=$post->count_reviews?>
            <?=Yii::$app->formatter->getNumEnding($post->count_reviews, [
                'отзыв', 'отзыва', 'отзывов'
            ])?>
        </div>
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
            <?php if ($isShowDiscounts):?>
                <a href="<?=Url::to(['post/get-discounts-by-post', 'name' => $post['url_name'], 'postId' => $post['id']])?>">
                    <div class="btn2-menu"><span class="under-line">Скидки <?=$discountCount?></span></div>
                </a>
            <?php endif;?>
        </div>
    </div>
</div>
<div class="block-content">
    <div class="block-photos-container">
        <div class="block-photos" data-type="all">
            <?php foreach ($previewPhoto as $index => $photo):?>
                <meta itemprop="image" content="<?=Yii::$app->params['site.hostName'].$photo->getPhotoPath()?>">
                <div class="photo n<?=$index+1?>" style="background-image: url('<?=$photo->getPhotoPath()?>')" data-sequence="<?=$index?>"></div>
            <?php endforeach;?>
            <?php if(count($previewPhoto) == 0):?>
                <meta itemprop="image" content="<?=Yii::$app->params['site.hostName']?>/default_img.jpg">
            <?php endif;?>
            <?php for ($i = count($previewPhoto); $i < 4; $i++):?>
                <div class="photo-not-found n<?=$i+1?>"></div>
            <?php endfor;?>
        </div>
        <div class="block-photos-bottom">
            <div class="block-photos-text">Фотографии <?=$photoCount?></div>
            <label class="btn-add-photo photo-upload-sign" for="post-photos">Добавить фото</label>
            <input type="file" name="post-photos" id="post-photos" style="display: none;" multiple
                   accept="image/*,image/jpeg,image/gif,image/png" data-id="<?=$post->id?>">
        </div>
    </div>
</div>
<div class="block-content">

    <div class="container-columns">
        <div class="__first-column">
            <div class="block-content-between cust">
                <h2 class="h2-v">Информация</h2>
                <noindex>
                    <p class="text p-text main-pjax">
                        Нашли неточность или ошибку,&nbsp;<a class="href-edit" href="/edit/<?=$post['id']?>" rel="nofollow">
                            исправьте&nbsp;или&nbsp;дополните&nbsp;информацию</a>
                    </p>
                </noindex>
            </div>
            <div class="block-info-card">
                <?php if($post['address']):?>
                    <div class="info-row">
                        <div class="left-block-f1">
                            <div class="address-card"><span>Адрес</span></div>
                            <div class="block-inside">
                                <p itemprop="address" class="info-card-text"><?=$post->city['name'].', '.$post['address']?></p>
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
                                        <li itemprop="telephone"><a href="tel:<?=$phone?>"><?= $phone ?></a></li>
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
                                    <a target="_blank" href="<?= '/away?to=' . urlencode(Url::ensureScheme('//' . preg_replace('/https?:\/\//', '', $post->info['web_site']), 'http'))?>">
                                        <?= Yii::$app->formatter->asHostName($post->info['web_site']) ?>
                                    </a>
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
                                                <a target="_blank" href="<?= '/away?to=' . urlencode($valueItem)?>" class="<?=$keyItem?>-icon"></a>
                                            <?php endforeach;?>
                                        <?php else:?>
                                            <a target="_blank" href="<?='/away?to=' . urlencode($social_network)?>" class="<?=$key?>-icon"></a>
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
                <?php if ($post->info && is_array($post->info->editors_users) && $post->info->editors_users): ?>
                    <noindex>
                        <div class="info-row">
                            <div class="left-block-f">
                                <div class="title-info-card">Редакторы</div>
                                <div class="block-inside user-editor">
                                    <div class="container-user-editor">
                                        <ul>
                                            <?php foreach ($post->info->editors_users as $editor): ?>
                                                <li>
                                                    <a href="/id<?= $editor->id ?>" rel="nofollow">
                                                        <img src="<?= $editor->getPhoto() ?>">
                                                        <span><?=$editor->name.' '.$editor->surname?></span>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="right-block-f">
                                <div class="btn-info-card"></div>
                            </div>
                        </div>
                    </noindex>
                <?php endif; ?>
            </div>
            <?php if($post->info && $post->info->article):?>
                <h2 class="h2-c">Описание</h2>
                <div class="block-description-card">
                    <?=$post->info->article?>
                </div>
            <?php endif;?>
            <div class="block-content-between">
                <noindex>
                    <div class="block-social-share">
                        <div class="social-btn-share goodshare" data-type="vk"><p>Поделиться</p> <span data-counter="vk">0</span></div>
                        <div class="social-btn-share goodshare" data-type="fb"><p>Share</p><span data-counter="fb">0</span></div>
                        <div class="social-btn-share goodshare" data-type="tw"><p>Твитнуть</p></div>
                        <div class="social-btn-share goodshare" data-type="ok"><span data-counter="ok">0</span></div>
                    </div>
                </noindex>
                <div class="block-count-views">
                    <div class="elem-count-views"><?=$post->totalView['count']?></div>
                </div>
            </div>

            <?php if (isset($dataProviderDiscounts) && $dataProviderDiscounts->getTotalCount() > 0):?>
            <noindex>
                <div class="block-content-between cust">
                    <h2 class="h2-v">Вам может понравиться</h2>
                    <p class="text p-text">
                        <a class="--promo-link" href="<?= Url::to(['lading/sale-of-a-business-account'])?>"
                            rel="nofollow">
                        Разместить свою акцию
                        </a>
                    </p>
                </div>

                <div class="cards-block-discount row-3 main-pjax"
                     data-favorites-state-url="/discount/favorite-state" style="margin-top: -13px;">

                    <?= CardsDiscounts::widget([
                        'dataprovider' => $dataProviderDiscounts,
                        'settings' => [
                            'show-more-btn' => true,
                            'replace-container-id' => 'feed-discounts',
                            'load-time' => $loadTime,
                            'postId' => $post->id,
                            'show-distance' => true,
                            'links-no-follow' => true,
                            'hide-bottom-block' => true,
                        ]
                    ]); ?>
                </div>
            </noindex>
            <?php endif;?>

            <!--<div class="comments_entity_container" data-entity_type="3" data-entity_id="<?/*=$post['id']*/?>">
                <?/*=$this->render('/comments/post_comments',['dataProviderComments'=>$dataProviderComments,'totalComments'=>$post->totalComments])*/?>
            </div>-->

            <?=$this->render('__reviews',['reviewsDataProvider'=>$reviewsDataProvider,'post'=>$post, 'type' => $type, 'loadTime' => $loadTime])?>
            <?php if(!$post->has_send_bs):?>
                <noindex>
                    <div class="block-info-for-owner" data-post_id="<?=$post->id?>">
                        <p>Вы владелец этого места? Зарегистрируйте бесплатный бизнес-аккаунт и отвечайте на отзывы и вопросы от имени компании.</p>
                    </div>
                </noindex>
            <?php endif;?>
            <div class="margin-top60"></div>

        </div>
        <div class="__second-column">
            <div class="--top-50px"></div>
            <?php if (isset($dataProviderRecommendedPosts) && $dataProviderRecommendedPosts->getTotalCount() > 0):?>
                <noindex>
                    <div class="recommended-block cards-block">
                        <?= CardsRecommendedPlace::widget([
                            'dataprovider' => $dataProviderRecommendedPosts,
                            'settings' => [
                                'links-no-follow' => true,
                            ],
                        ]); ?>
                    </div>
                    <div class="--promo-block">
                        <a href="<?= Url::to(['lading/sale-of-a-business-account'])?>" class="--promo-link"
                           rel="nofollow">
                            Продвинуть свой бизнес
                        </a>
                    </div>
                </noindex>
            <?php endif;?>
            <?= RightBlockWidget::widget()?>
        </div>
    </div>

</div>
</div>

<input style="display: none" class="photo-add-review" name="photo-add-review" type="file" multiple
       accept="image/*,image/jpeg,image/gif,image/png">

<?= PhotoSlider::widget([
    'settings' => [
        'post' => $post,
        'photoCount' => $photoCount,
    ],
])?>

<?php if($review_id = Yii::$app->request->get('review_id',false)):?>
    <script>
        $(document).ready(function() {
            reviews.scrollToFirstReviews(<?=$review_id?>);
        });
    </script>
<?php endif;?>
<script>
    $(document).ready(function() {
        post.info.init();
        post.photos.setLoadTime(<?=time()?>);
        post.photos.setPostId(<?=$post->id?>);
        post.photos.setAllPhotoCount(<?=$photoCount?>);

        <?php if (isset($initPhotoSliderParams['photoId'])) :?>
            post.photos.initPhotoSlider({
                photoId: '<?=$initPhotoSliderParams['photoId']?>',
                reviewId: <?=$initPhotoSliderParams['reviewId'] ?? 'null'?>,
                type: '<?=$initPhotoSliderParams['reviewId'] ? 'review' : 'all'?>'
            });
        <?php endif;?>

		menu_control.fireMethodClose();
        search.clear();
        comments.init(3);
        comments.setAutoResize('.textarea-main-comment');

        <?php if ($message = Yii::$app->session->getFlash('message')):?>
            $().toastmessage('showToast', {
                text: '<?=$message['text']?>',
                stayTime: 8000,
                type: '<?=$message['type']?>'
            });
        <?php endif;?>
    })
</script>

<?php
Pjax::end();
?>
