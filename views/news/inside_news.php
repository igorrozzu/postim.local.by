<?php
use app\components\breadCrumb\BreadCrumb;
use app\components\cardsNewsWidget\CardsNewsWidget;
use app\components\commentsWidget\CommentsNewsWidget;
$this->title = $news['title_s'];
$this->registerMetaTag([
    'name' => 'description',
    'content' => $news['description_s'],
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content'=> $news['key_word_s']
]);
?>
<div class="margin-top60"></div>
<div class="block-content">
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
            <div class="date-time-text"><?=Yii::$app->formatter->printDate($news['date'])?></div>
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
    <div class="comments_entity_container" data-entity_id="<?=$news['id']?>">
        <?=$this->render('/comments/comments',['dataProviderComments'=>$dataProviderComments,'totalComments'=>$news->totalComments])?>
    </div>
    <noindex>
        <h2 class="h2-c">Последние новости</h2>
        <div class="block-news-container">
            <div class="block-news">
                <?= CardsNewsWidget::widget(['dataprovider' => $lastNews, 'settings' => ['replace-container-id' => 'feed-news','load-time'=>$loadTime]]) ?>
            </div>
        </div>
    </noindex>
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