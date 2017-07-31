<?php
use app\components\breadCrumb\BreadCrumb;
use app\components\cardsNewsWidget\CardsNewsWidget;
use app\components\commentsWidget\CommentsNewsWidget;
$this->title = $news['header'];
?>
<div class="margin-top60"></div>
<div class="block-content">
    <div class="container-post">
        <?= BreadCrumb::widget(['breadcrumbParams'=>$breadcrumbParams])?>
        <h1 class="h1-v"><?=$news['header']?></h1>
        <div class="block-between page-news">
            <div class="block-info-reviewsAndfavorites">
                <div class="add-favorite <?=$news['is_like']?'active':''?>"></div>
                <div class="total-comments"><?=$news->totalComments?></div>
            </div>
            <div class="date-time-text"><?=Yii::$app->formatter->printDate($news['date'])?></div>
        </div>
        <div class="container-content-post">
            <?=$news['data']?>
        </div>
    </div>
    <div class="block-content-between">
        <div class="block-social-share">
            <div class="social-btn-share"><p>Поделиться</p> <span>24</span></div>
            <div class="social-btn-share"><span>3</span></div>
            <div class="social-btn-share"><p>Твитнуть</p></div>
            <div class="social-btn-share"><p>Поделиться</p><span>153</span></div>
        </div>
        <div class="block-count-views">
            <div class="elem-count-views"><?=$news->totalView['count']?></div>
        </div>
    </div>
    <h2 class="h2-c">Комментарии <span class="total"><?=$news->totalComments?></span></h2>
    <div class="block-сomments-post">
        <?=CommentsNewsWidget::widget(['dataprovider'=>$dataProviderComments])?>
    </div>
    <h2 class="h2-c">Последнии новости</h2>
    <div class="block-news-container">
        <div class="block-news">
            <?= CardsNewsWidget::widget(['dataprovider' => $lastNews, 'settings' => ['replace-container-id' => 'feed-news','load-time'=>$loadTime]]) ?>
        </div>
    </div>
</div>
<div class="clear-fix"></div>
<div style="margin-bottom:30px;"></div>