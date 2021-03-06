<?php
use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
use yii\helpers\Url;

$reviews = $dataProvider->getModels();
?>

<?php foreach ($reviews as $review):?>
    <div style="<?=$review->status==\app\models\Reviews::$STATUS['private']?'background: #ffff66;':''?>" class="block-reviews <?=!($settings['without_header']??false)?'':'without_header'?>" data-reviews_id="<?=$review->id?>" data-type="review">
        <?php if(!($settings['without_header']??false)):?>
            <a href="<?=Url::to(['post/index', 'url' => $review->post['url_name'], 'id' => $review->post['id']])?>">
                <div class="block-review-post" style="border-bottom: solid 1px #D3E4FF; padding: 20px;">
                    <div class="rating-r bg-r<?=$review->post->rating?>"><?=$review->post->rating?></div>
                    <div class="block-info-review main-pjax">
                        <p class="user-name"><?=\yii\helpers\Html::encode(\yii\helpers\Html::decode($review->post->data))?></p>
                        <div class="date-time-review"><?=implode(', ',ArrayHelper::getColumn($review['post']['categories'],'name')) ?></div>
                    </div>
                </div>
            </a>
        <?php endif;?>
        <div class="review-header">
            <a href="/id<?=$review->user->id?>"><img class="profile-icon" src="<?=$review->user->getPhoto()?>"></a>
                <div class="block-info-review">
                    <div class="block-info-review-user">
                        <div class="block-between">
                            <a href="/id<?=$review->user->id?>">
                                <p class="user-name"><?=$review->user->name . ' ' . $review->user->surname;?></p>
                            </a>
                            <div class="user-level"><?=$review->user->userInfo->level?>
                                <noindex>
                                    <span>&nbsp;уровень</span>
                                </noindex>
                            </div>
                        </div>
                    </div>
                    <div class="date-time-review">
                        <?=Yii::$app->formatter->printDate($review->date + Yii::$app->user->getTimezoneInSeconds())?>
                    </div>
                </div>

        </div>
        <div class="block-review-content">
            <div class="rating-r bg-r<?=$review->rating?>"><?=$review->rating?></div>
            <div class="review-help-text">
                <noindex>Оценка</noindex>
            </div>
            <?php if($settings['noIndexData']??false):?>
                <div class="review-text"><noindex><?=$review->data?></noindex></div>
            <?php else:?>
                <div class="review-text"><?=$review->data?></div>
            <?php endif; ?>

        </div>
        <?php $photo = $review->getLastPhoto(); ?>
        <?php if($photo && $review->count_photos):?>
            <div class="review-photo" style="background-image: url('<?=$photo->getPhotoPath()?>')" data-sequence="0">
                <div class="block-total-photo-review"><?=$review->count_photos?></div>
            </div>
        <?php endif;?>
        <?php if($review->officialAnswer):?>
            <?=$this->render('__answer',['item'=>$review->officialAnswer])?>
        <?php endif;?>
        <div class="review-footer">
            <div class="review-footer-btn btn-like <?=$review->is_like?'active':''?>"><?=$review->like?></div>
            <?php
                $textComm=$review->totalComments?$review->totalComments:'Ответить';
            ?>
            <div class="review-footer-btn btn-comm" data-text="<?=$review->totalComments?>"><?=$textComm?></div>
            <?php if(Yii::$app->user->getId()!=$review['user_id']):?>
                <?php if(!$review->is_complaint):?>
                    <div class="review-footer-btn btn-complaint">
                        <noindex>Пожаловаться</noindex>
                    </div>
                <?php endif;?>
            <?php elseif($settings['without_header']??false):?>
                <div class="review-footer-btn btn-edit-reviews">Редактировать</div>
            <?php endif;?>
        </div>
        <div class="container-reviews-comments"></div>
    </div>
<?php endforeach;?>

<?php if($settings['show-more-btn'] &&
    ($hrefNext = $dataProvider->pagination->getLinks()['next'] ?? false)):?>
    <div class="review-show-more" id="<?=$settings['replace-container-id']?>">
        <div class="btn-show-more" data-selector_replace="#<?=$settings['replace-container-id']?>"
             data-href="<?=$hrefNext?>&loadTime=<?=$settings['load-time']?>">
            <noindex>Показать больше отзывов</noindex>
        </div>
    </div>
<?php endif;?>




