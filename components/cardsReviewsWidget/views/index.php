<?php
use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
use yii\helpers\Url;

$reviews = $dataProvider->getModels();
?>

<?php foreach ($reviews as $review):?>
    <div class="block-reviews">
        <div class="block-review-content" style="margin-top: 20px;">
            <div class="rating-r bg-r4"><?=$review->post->rating?></div>
            <div class="block-info-review" style="border-bottom: solid 1px #D3E4FF; padding-bottom: 23px;">
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
                    </div>
                    <div class="date-time-review">
                        <?=Yii::$app->formatter->printDate($review->date + $review->user->getTimezoneInSeconds())?>
                    </div>
                </div>
            </div>
        </div>
        <div class="block-review-content">
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




