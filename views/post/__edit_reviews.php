<div class="block-write-reviews active edit_reviews" data-post_id="<?=$reviews['post_id']?>" data-reviews_id="<?=$reviews['id']?>">
    <div class="profile-user-reviews">
        <img class="profile-icon60x60" src="<?=Yii::$app->user->getPhoto()?>">
        <?=Yii::$app->user->getName()?>
    </div>
    <div class="container-write-reviews">
        <form class="form-write-reviews">
            <input id="input_reviews_post_id" name="reviews[post_id]" value="<?=$reviews['post_id']?>" type="hidden">
            <input name="id" value="<?=$reviews['id']?>" type="hidden">
            <div class="add-review-label mark"><?=\app\components\Helper::getTextMarkReviews($reviews['rating'])?></div>
            <div class="container-evaluations">
                <input id="input-evaluation" name="reviews[rating]" type="hidden" value=" <?=$reviews['rating']?>">
                <div class="evaluation <?=$reviews['rating']==1?'active':''?>">1</div>
                <div class="evaluation <?=$reviews['rating']==2?'active':''?>">2</div>
                <div class="evaluation <?=$reviews['rating']==3?'active':''?>">3</div>
                <div class="evaluation <?=$reviews['rating']==4?'active':''?>">4</div>
                <div class="evaluation <?=$reviews['rating']==5?'active':''?>">5</div>
            </div>
            <div class="add-review-label">Напишите отзыв</div>
            <div class="block-textarea-review">
                <textarea name="reviews[data]"
                          placeholder="Пожалуйста, аргументируйте свою оценку. Напишите не менее 100 символов. Расскажите в деталях о своем опыте. Что заслуживает отдельного внимания? Рекомендуете или нет?"
                          class="textarea-review"
                          style="overflow: hidden; overflow-wrap: break-word; height: 150px;"><?=$reviews['data']?></textarea>
                <div class="container-insert-photos">
                    <div class="container-photos-inputs">
                        <?php foreach ($reviews->gallery as $item):?>
                            <input id="inputs_<?=md5($item['link'])?>" style="display: none" name="reviews[photos][]" value="<?=$item['link']?>" type="text">
                        <?php endforeach;?>
                    </div>
                    <div class="block-tmp-photos">
						<?php foreach ($reviews->gallery as $item):?>
                            <div id="<?=md5($item['link'])?>" class="review-photo-tmp" style='background-image: url("<?=$item->getPhotoPath()?>");'>
                                <div class="close-add-photo"></div>
                            </div>
						<?php endforeach;?>
                    </div>
                </div>
                <div class="block-btns-textarea-review">
                    <div class="btn-add-photo-review"><p>Добавить фото</p></div>
                    <div class="btn-rule"><a href="#">Правила размещения отзывов</a></div>
                </div>
            </div>
        </form>
    </div>
    <div class="large-wide-button btn-send-reviews"><p>Редактировать</p></div>
</div>