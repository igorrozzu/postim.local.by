<?php
use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
use yii\helpers\Url;

$reviews = $dataProvider->getModels();
?>

<?php foreach ($reviews as $review):?>
    <div class="block-reviews">
        <div class="block-review-post" style="margin-top: 20px; border-bottom: solid 1px #D3E4FF; padding-bottom: 23px;">
            <div class="rating-r bg-r4"><?=$review->post->rating?></div>
            <div class="block-info-review">
            <p class="user-name"><?=$review->post->data?></p>
            <div class="date-time-review"><?=$review->post->underCategory->name?></div>
            </div>
        </div>
        <div class="review-header">
            <img class="profile-icon40x40" src="<?=$review->user->getPhoto()?>">
            <div class="block-between">
                <div class="block-info-review">
                    <div class="block-info-review-user">
                        <p class="user-name"><?=$review->user->name . ' ' . $review->user->surname;?></p>
                        <span class="user-points"><?=$review->user->userInfo->exp_points?></span>
                    </div>
                    <div class="date-time-review">
                        <?=Yii::$app->formatter->printDate($review->date + Yii::$app->user->getTimezoneInSeconds())?>
                    </div>
                </div>

                <div class="block-btn-social-share" is-open="1">
                    <div class="btn-circle-share"></div>
                    <div class="btn-circle-share"></div>
                    <div class="btn-circle-share"></div>
                    <div class="btn-circle-share"></div>
                </div>
                <div class="block-btn-circle-share">
                    <div class="btn-social-share open-share"></div>
                </div>
            </div>
        </div>
        <div class="block-review-content">
            <div class="rating-r bg-r4"><?=$review->rating?></div>
            <div class="review-help-text">Оценка</div>
            <div class="block-btn-m-social-share" is-open="0" style="right: -160px;">
                <div class="btn-circle-share"></div>
                <div class="btn-circle-share"></div>
                <div class="btn-circle-share"></div>
                <div class="btn-circle-share"></div>
            </div>
            <div class="block-btn-m-circle-share">
                <div class="btn-social-share open-share"></div>
            </div>

            <div class="review-text"><?=$review->data?></div>
        </div>
        <div class="review-photo" style="background-image: url('/testP.png')">
            <div class="block-total-photo-review">12</div>
        </div>
        <div class="review-footer">
            <div class="review-footer-btn btn-like"><?=$review->like?></div>
            <div class="review-footer-btn btn-comm">Ответить</div>
            <div class="review-footer-btn">Пожаловаться</div>
        </div>
    </div>
<?php endforeach;?>

<?php if($settings['show-more-btn'] &&
    ($hrefNext = $dataProvider->pagination->getLinks()['next'] ?? false)):?>
    <div class="review-show-more" id="<?=$settings['replace-container-id']?>">
        <div class="btn-show-more" data-selector_replace="#<?=$settings['replace-container-id']?>"
             data-href="<?=$hrefNext?>&loadTime=<?=$settings['load-time']?>">
            Показать больше отзывов
        </div>
    </div>
<?php endif;?>




