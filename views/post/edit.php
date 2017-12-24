<div class="margin-top60"></div>
<div class="block-content">
	<form action="/post/save-edit-post" id="post-form" method="post">
        <?php if(isset($params['post']['main_id'])):?>
            <input type="hidden" name="id" value="<?=$params['post']->main_id?>">
        <?php else:?>
            <input type="hidden" name="id" value="<?=$params['post']->id?>">
        <?php endif;?>
		<?php if ($params['post']->info->editors??false): ?>
            <?php foreach ($params['post']->info->editors as $editor):?>
                <input type="hidden" name="editors[]" value="<?=$editor?>">
            <?php endforeach;?>
		<?php endif; ?>
		<div class="container-add-place">
			<div class="block-field-setting">
				<label class="label-field-setting">Название места</label>
				<input name="name" class="input-field-setting validator" data-error-parents="block-field-setting" data-message="Ввидите название" data-regex="^\S.{3,}" placeholder="Введите название" value="<?=$params['post']['data']?>">
			</div>
			<div class="block-field-setting">
				<label class="label-field-setting">Категория</label>
				<div class="selectorFields" id="categories" data-is-many="true" data-id="categories" data-max="3"
					 data-info='<?= \yii\helpers\Json::encode($params['categories']) ?>'>
					<div class="block-inputs">
						<?php
							$categories = $params['post']->getCategoriesPriority();
							foreach ($categories as $category):
						?>
								<div class="btn-selected-option"><span class="option-text"><?=$category['name']?></span> <span class="close-selected-option"></span> <input name="categories[]" value="<?=$category['id']?>" style="display: none"> </div>
							<?php endforeach;?>
					</div>
					<div class="between-selected-field btn-open-field" data-open=false>
						<input class="search-selected-field" type="button" data-value="Выберите категорию"
							   value="Выберите категорию" placeholder="Выберите категорию">
						<div class="open-select-field2"></div>
					</div>
					<div class="container-scroll-fields">
						<div class="container-options"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="container-add-place">
			<div class="block-field-setting" style="border-bottom: 0px">
				<label class="label-field-setting">Адрес</label>
				<div class="selectorFields" data-is-many="false" data-id="city" data-max="1"
					 data-info='<?= \yii\helpers\Json::encode($params['cities']) ?>'>
					<div class="block-inputs" style="display: none">
                        <div class="btn-selected-option"><span class="option-text"><?=$params['post']->city->name?></span> <span class="close-selected-option"></span> <input name="city" value="<?=$params['post']->city->id?>" style="display: none"> </div>
                    </div>
					<div class="between-selected-field btn-open-field" data-open=false>
						<input style="color: rgb(68, 68, 68);" class="search-selected-field" type="button" data-value="<?=$params['post']->city->name?>"
							   value="<?=$params['post']->city->name?>" placeholder="Выберите город">
						<div class="open-select-field2"></div>
					</div>
					<div class="container-scroll-fields">
						<div class="container-options"></div>
					</div>
				</div>
			</div>

			<div class="block-field-setting" style="border-bottom: 0px">
				<input style="margin-top: 0px" name="address_text"  class="input-field-setting validator" placeholder="Введите адрес" data-error-parents="block-field-setting" data-message="Введите адрес" data-regex="^\S.{3,}" value="<?=$params['post']->address?>">
				<label class="label-field-setting">Комментарий к адресу</label>
				<input name="comments_to_address" class="input-field-setting" placeholder="Расстояние до метро, этаж и т.д."
					   value="<?=$params['post']->additional_address?>">
				<input id="coords_address" style="display: none" name="coords_address" type="text" value="<?=$params['post']->lat?>,<?=$params['post']->lon?>">
			</div>

            <div class="block-field-setting metro" style="margin-bottom: -20px;border-bottom: 0px;<?=$params['post']->city->name=='Минск'?'':'display:none'?>">
                <label class="label-field-setting">Метро</label>
                <div class="selectorFields" data-is-many="false" data-id="metro" data-max="1"
                     data-info='<?= \yii\helpers\Json::encode($params['metro']) ?>'>
                    <div class="block-inputs" style="display: none">
                        <?php if($params['post']->metro):?>
                            <div class="btn-selected-option"><span class="option-text"><?=$params['post']->metro?></span> <span class="close-selected-option"></span> <input name="metro" value="<?=$params['post']->metro?>" style="display: none"> </div>
                        <?php endif;?>
                    </div>
                    <div class="between-selected-field btn-open-field" data-open=false>
                        <input class="search-selected-field" type="button" data-value="Выберите метро"
                               value="<?=$params['post']->metro?$params['post']->metro:'Выберите метро'?>" placeholder="Выберите метро" <?=$params['post']->metro?'style="color: rgb(68, 68, 68);"':$params['post']->metro?>>
                        <div class="open-select-field2"></div>
                    </div>
                    <div class="container-scroll-fields">
                        <div class="container-options"></div>
                    </div>
                </div>
            </div>

            <div class="block-field-setting" style="margin-bottom: -20px;border-bottom: 0px">
                <label class="label-field-setting">Поиск по карте</label>
                <input class="input-field-setting js-search-on-map" placeholder="Введите адрес, чтобы отметить место" value="">
            </div>

			<div id="map_block" class="block-map">
				<div class="btns-map">
					<div class="find-me" title="Найти меня"></div>
					<div class="zoom-plus"></div>
					<div class="zoom-minus"></div>
				</div>
				<div id="map" style="display: block"></div>
			</div>
		</div>

		<div class="container-add-place">
			<div class="block-field-setting">
				<label class="label-field-setting">Добавить контакт</label>

                <?php
                   echo $this->render('__contacts_edit',['postInfo'=>$params['post']->info])
                ?>

				<div class="selected-field">
					<div id="select-contact-value" class="select-value"><span
							class="placeholder-select">Выберите контакт</span></div>

					<div data-open-id="select-contact" class="open-select-field"></div>
				</div>
				<div id="select-contact" class="container-scroll auto-height">
					<div class="container-option-select add-field-contact">
						<div data-value="Телефон" class="option-select-field">Телефон</div>
						<div data-value="Веб-сайт" class="option-select-field" <?=$params['post']->info->web_site?'style="display:none;"':''?>>Веб-сайт</div>
						<div data-value="Вконтакте" class="option-select-field">Вконтакте</div>
						<div data-value="Одноклассники" class="option-select-field">Одноклассники</div>
						<div data-value="Twitter" class="option-select-field">Twitter</div>
						<div data-value="Facebook" class="option-select-field">Facebook</div>
						<div data-value="Instagram" class="option-select-field">Instagram</div>
					</div>
				</div>
			</div>
		</div>
		<div class="container-add-place">
			<div class="block-field-setting">
				<label class="label-field-setting">Режим работы</label>
                <?=$this->render('__worktime_edit',['post'=>$params['post']])?>
			</div>
		</div>

		<div class="container-add-place block-features">
            <?=$this->render('__edit_features',['features' => $params['features']])?>
		</div>

        <div class="container-add-place">
            <div class="block-field-setting">
                <label class="label-field-setting">Реквизиты</label>
                <input name="requisites" class="input-field-setting validator" data-error-parents="block-field-setting"  placeholder="Введите реквизиты: ООО, УНП и т.д." value="<?=$params['post']->requisites?>">
            </div>
        </div>


		<div class="container-add-place">
			<div class="container-description">
				<div class="description-header">Описание места</div>
				<div class="block-write-editors">
					<input id="article" name="article" value="<?=$params['post']->info->article?\yii\helpers\Html::encode($params['post']->info->article):''?>" type="text" style="display: none">
                    <?php if($params['post']->info->article):?>
						<?=\app\components\Helper::parserForEditor($params['post']->info->article,true);?>
                    <?php else:?>
                        <div class="item item-editor-default container-editor"><div class="editable"></div></div>
                    <?php endif;?>

				</div>
			</div>
		</div>

		<script>
			$(document).ready(function () {
				editable.init('.editable', {
					toolbar: {
					    photo : true,
						video: true,
						text: true
					}
				})
			});
		</script>

		<?php if(Yii::$app->user->identity->role > 1 ||
            ($params['post']['status']==0 &&
                !isset($params['post']['main_id']) &&
                $params['post']['user_id'] == Yii::$app->user->getId())
        ):?>
			<div class="container-add-place">
				<div class="container-gallery">
					<div class="gallery-header">Галерея</div>
					<?=$this->render('__edit_photos',['post'=>$params['post'],'photos'=>$params['photos']])?>
					<div class="btn-add-photos-gallery">Добавить фотографии</div>
				</div>
				<input style="display: none" class="photo-add" name="photo-add" type="file" multiple accept="image/*,image/jpeg,image/gif,image/png">
			</div>
		<?php endif;?>

			<div class="container-add-place">
				<?php if(Yii::$app->user->identity->role > 1):?>
                <?php

                    if(!$params['post']->title){
                        $params['post']->title = $params['post']->data.', '.
                            mb_strtolower(Yii::t('app/singular',$params['post']->onlyOnceCategories[0]->name)).' в '.
                            Yii::t('app/locativus',$params['post']->city->name).', '.
                            ': отзывы, адрес, телефоны и карта проезда';
                    }

                    if(!$params['post']->description){
                        $params['post']->description = Yii::t('app/singular',$params['post']->onlyOnceCategories[0]->name).' '.
                            $params['post']->data.' в '.
                            Yii::t('app/locativus',$params['post']->city->name).', '.
                            $params['post']->address.'. Отзывы посетителей, адрес и время работы — удобный поиск на карте Postim.by!';
                    }

                    if(!$params['post']->key_word){
                        $params['post']->key_word = mb_strtolower(Yii::t('app/singular',$params['post']->onlyOnceCategories[0]->name)).' '.
                            $params['post']->data.', '.
                            $params['post']->city->name;
                    }

                ?>
				<div class="block-field-setting">
					<label class="label-field-setting">Заголовок для поисковиков</label>
					<input name="engine[title]" class="input-field-setting" placeholder="Введите текст" value="<?=$params['post']['title']?$params['post']['title']:''?>">
				</div>
				<div class="block-field-setting">
					<label class="label-field-setting">Описание для поисковиков</label>
					<input name="engine[description]" class="input-field-setting" placeholder="Введите текст" value="<?=$params['post']['description']?$params['post']['description']:''?>">
				</div>
				<div class="block-field-setting">
					<label class="label-field-setting">Ключевые слова</label>
					<input name="engine[key_word]" class="input-field-setting" placeholder="Введите текст" value="<?=$params['post']['key_word']?$params['post']['key_word']:''?>">
				</div>
                <div class="btn-place-save">
                    <div class="large-wide-button"><p>Редактировать</p></div>
                </div>
				<?php else:?>
                    <div class="btn-place-save" style="margin-top: -20px">
                        <div class="large-wide-button"><p>На модерацию</p></div>
                    </div>
                <?php endif;?>


			</div>

	</form>
</div>
<div style="margin-bottom:30px;"></div>
<script>
	$(document).ready(function () {
		$('.block-inputs').sortable();

		var maskBehavior = function (val) {
			val = val.split(":");
			return (parseInt(val[0]) > 19)? "HZ:M0" : "H0:M0";
		};

		spOptions = {
			onKeyPress: function(val, e, field, options) {
				field.mask(maskBehavior.apply({}, arguments), options);
			},
			translation: {
				'H': { pattern: /[0-2]/, optional: false },
				'Z': { pattern: /[0-3]/, optional: false },
				'M': { pattern: /[0-5]/, optional: false}
			}
		};

		$('.container-time .time-period input').mask(maskBehavior, spOptions);
		map.map_block = null;
		map.init({lat:<?=$params['post']->lat?>, lon:<?=$params['post']->lon?>},16);

		post_add.validation.check_change(true);

		map.setMarkerOnMap({lat:<?=$params['post']->lat?>,lng:<?=$params['post']->lon?>},post_add.setPlace);

	})
</script>