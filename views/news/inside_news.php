<?php
use app\components\breadCrumb\BreadCrumb;
use app\components\cardsNewsWidget\CardsNewsWidget;
use app\components\commentsWidget\CommentsNewsWidget;
use app\widgets\cardsDiscounts\CardsDiscounts;
use yii\helpers\Url;

$this->title = $news['title_s'];
$this->registerMetaTag([
    'name' => 'description',
    'content' => $news['description_s'],
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content'=> $news['key_word_s']
]);


$paramsMetaTagsOg = [
    'og:locale'=>'ru_RU',
    'og:type'=>'article',
    'og:title'=>$news['title_s'],
    'twitter:title'=>$news['title_s'],
    'og:description'=>$news['description_s'],
    'twitter:description'=>$news['description_s'],
    'og:url'=>\yii\helpers\Url::to('',true),
    'og:site_name'=>'Postim.by',
    'twitter:site'=>'Postim.by',
];

preg_match('/(?<=div class="block-photo-post"><img src=")\/.+?(?=")/',$news['data'],$match);

if($match){
    $paramsMetaTagsOg['og:image'] = Yii::$app->params['site.hostName'].$match[0];
    $paramsMetaTagsOg['twitter:image:src'] = Yii::$app->params['site.hostName']. $match[0];
}else{
    $paramsMetaTagsOg['og:image'] = Yii::$app->params['site.hostName'].'/default_img.jpg';
    $paramsMetaTagsOg['twitter:image:src'] = Yii::$app->params['site.hostName']. '/default_img.jpg';
}

\app\components\MetaTagsSocialNetwork::initOg($this,$paramsMetaTagsOg);


?>
<div class="margin-top60"></div>
<div class="block-content">

    <div class="container-columns">
        <div class="__first-column">

            <?php if(Yii::$app->user->isModerator()):?>
                <div class="block-content-between" style="margin-bottom: -10px">
                    <p class="text p-text">
                        Нашли неточность или ошибку,&nbsp;<a class="href-edit" href="/admin/news/edit-news?id=<?=$news['id']?>">исправьте&nbsp;или&nbsp;дополните&nbsp;информацию</a>
                    </p>
                </div>
            <?php endif;?>
            <div class="container-post">
                <?= BreadCrumb::widget(['breadcrumbParams'=>$breadcrumbParams])?>
                <h1 class="h1-v"><?=$news['header']?></h1>
                <div class="block-between page-news">
                    <div class="block-info-reviewsAndfavorites" data-item-id="<?=$news['id']?>" data-type="news">
                        <div class="add-favorite <?=$news['is_like']?'active':''?>"><?=$news->count_favorites?></div>
                    </div>
                    <div class="date-time-text"><?=Yii::$app->formatter->printDate($news['date']+Yii::$app->user->getTimezoneInSeconds())?></div>
                </div>
                <div class="container-content-post">
                    <?=$news['data']?>
                </div>
            </div>
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
                    <div class="elem-count-views"><?=$news->totalView['count']?></div>
                </div>
            </div>
            <div class="comments_entity_container" data-entity_type="1" data-entity_id="<?=$news['id']?>">
                <?=$this->render('/comments/comments', [
                        'dataProviderComments' => $dataProviderComments,
                        'totalComments' => $news->totalComments
                    ])?>
            </div>
            <noindex>
                <h2 class="h2-c">Последние новости</h2>
                <div class="block-news-container">
                    <div class="block-news">
                        <?= CardsNewsWidget::widget([
                            'dataprovider' => $lastNews,
                            'settings' => [
                                'replace-container-id' => 'feed-news',
                                'load-time' => $loadTime
                            ]
                        ]) ?>
                    </div>
                </div>
                <?php if (isset($dataProviderDiscounts) && $dataProviderDiscounts->getTotalCount() > 0):?>
                    <div class="block-content-between cust">
                        <h2 class="h2-v">Вам может понравиться</h2>
                        <p class="text p-text">
                            <a class="--promo-link" href="<?= Url::to(['lading/sale-of-a-business-account'])?>"
                               rel="nofollow">
                                Разместить свою акцию
                            </a>
                        </p>
                    </div>
                    <div class="cards-block-discount row-3 main-pjax" style="margin-top: -13px;"
                         data-favorites-state-url="/discount/favorite-state">
                        <?= CardsDiscounts::widget([
                            'dataprovider' => $dataProviderDiscounts,
                            'settings' => [
                                'show-more-btn' => true,
                                'replace-container-id' => 'feed-discounts',
                                'load-time' => $loadTime,
                                'show-distance' => true,
                                'links-no-follow' => true,
                            ]
                        ]); ?>
                    </div>
                <?php endif;?>
            </noindex>

        </div>
        <div class="__second-column">
            <div class="--top-20px"></div>
            <?= \app\components\rightBlock\RightBlockWidget::widget()?>
        </div>
    </div>

</div>
<div class="clear-fix"></div>
<div style="margin-bottom:30px;"></div>
<?php
$js = <<<js
    $(document).ready(function() {
    	comments.init(1);
        comments.setAutoResize('.textarea-main-comment');
        menu_control.fireMethodClose();
        search.clear();
    })
js;

echo "<script>$js</script>";

?>