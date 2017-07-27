<?php
use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
use yii\helpers\Url;

$reviews = $dataProvider->getModels();
$nextPage = $dataProvider->pagination->getPage() + 1;
$pageCount = $dataProvider->pagination->getPageCount();
?>

<?php foreach ($reviews as $review):?>
    <div class="block-reviews">
        <div class="review-header">
            <img class="profile-icon60x60" src="<?=$review->user->getPhoto()?>">
            <div class="block-between">
                <div class="block-info-review">
                    <div class="block-info-review-user">
                        <p class="user-name"><?=$review->user->name . ' ' . $review->user->surname;?></p>
                        <span class="user-points"><?=$review->userInfo->exp_points?></span>
                    </div>
                    <div class="date-time-review"><?=Yii::$app->formatter->printDate($review->date)?></div>
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
            <div class="rating-r bg-r4"><?=$review->rating?></div>
            <div class="review-text"><?=$review->data?></div>
        </div>
        <div class="review-photo" style="background-image: url('testP.png')">
            <div class="block-total-photo-review">12</div>
        </div>
        <div class="review-footer">
            <div class="review-footer-btn btn-like"><?=$review->like?></div>
            <div class="review-footer-btn btn-comm">Ответить</div>
            <div class="review-footer-btn">Пожаловаться</div>
        </div>
    </div>
<?php endforeach;?>


<?php if($settings['show-more-btn']):?>
    <?php if ($nextPage < $pageCount):?>
        <div class="review-show-more" id="<?=$settings['replace-container-id']?>">
            <div class="btn-show-more" data-selector_replace="#<?=$settings['replace-container-id']?>"
                 data-href="<?=Url::to(['user/reviews', 'page' => $nextPage,
                'loadTime' => $settings['load-time'], 'id' => $settings['user-id']])?>">
                Показать больше отзывов</div>
        </div>
    <?php endif; ?>
<?php endif;?>



