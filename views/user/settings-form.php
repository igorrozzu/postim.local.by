<?php

use app\models\SocialAuth;
use app\models\UserInfo;
use yii\authclient\widgets\AuthChoice;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\widgets\ActiveForm;
use \yii\widgets\Pjax;

$user = Yii::$app->user->identity;
$userInfo = $user->userInfo;
$this->title = 'Персональные данные на Postim.by';
?>
<?php
Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => false,
    'id' => 'pjax-container-settings',
    'linkSelector' => false,
    'formSelector' => '#pjax-container-settings form',
]);
?>
<div class="margin-top60"></div>
<div class="block-content">
    <?php $form = ActiveForm::begin([
        'id' => 'user-settings-form',
        'enableClientScript' => false,
        'action' => '/user/settings',
        'options' => ['pjax-container-settings' => 'true'],
    ]) ?>
    <h1 class="h1-c center-mx" style="margin-top: 35px;">Персональные данные</h1>
    <div class="container-settings">

        <div class="user-icon-profile"><img src="<?= $user->getPhoto() ?>"></div>
        <label class="btn-add-icon" for="user-photo">Загрузить фото</label>
        <input type="file" name="user-photo" id="user-photo" style="display: none;"
               accept="image/*,image/jpeg,image/gif,image/png">


        <div class="block-field-setting">
            <label class="label-field-setting">Имя</label>
            <?= $form->field($model, 'name')
                ->textInput([
                    'style' => 'margin-bottom: 15px;',
                    'class' => 'input-field-setting',
                    'placeholder' => 'Введите имя',
                    'value' => $model->name ?? $user->name,
                ])
                ->label(false) ?>
        </div>
        <div class="block-field-setting">
            <label class="label-field-setting">Фамилия</label>
            <?= $form->field($model, 'surname')
                ->textInput([
                    'style' => 'margin-bottom: 15px;',
                    'class' => 'input-field-setting',
                    'placeholder' => 'Введите фамилию',
                    'value' => $model->surname ?? $user->surname,
                ])
                ->label(false) ?>
        </div>
        <div class="block-field-setting">
            <label class="label-field-setting">Город</label>
            <div class="selected-field">
                <div id="select-city-value" class="select-value" data-value
                <?php if ($user->isCityDefined() || $model->isCityDefined()): ?>
                ="<?= $model->cityId ?? $user->city_id ?>"
                    <?php endif; ?>>
                    <?php if ($userCityName === null): ?>
                        <span class="placeholder-select">Выберите город</span>
                    <?php else: ?>
                        <?= $userCityName ?>
                    <?php endif; ?>
                </div>
                <div data-open-id="select-city" class="open-select-field"></div>
            </div>
            <div id="select-city" class="container-scroll container-scroll-active">
                <div class="container-option-select option-active">
                    <?php foreach ($cities as &$city): ?>
                        <div data-value="<?= $city['id'] ?>" class="option-select-field"><?= $city['name'] ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?= $form->field($model, 'cityId')
                ->hiddenInput(['id' => 'select-city-hidden', 'value' => $model->cityId ?? $user->city_id])
                ->label(false) ?>
        </div>
        <div class="block-field-setting">
            <label class="label-field-setting">Пол</label>
            <div class="selected-field">
                <div id="select-sex-value" class="select-value" data-value
                <?php if ($userInfo->isGenderDefined() || $model->isGenderDefined()): ?>
                ="<?= $model->gender ?? $userInfo->gender ?>"
                    <?php endif; ?>>
                    <?php if ($userInfo->isGenderNotSelected() && $model->isGenderNotSelected()): ?>
                        <span class="placeholder-select">Выберите пол</span>
                    <?php else: ?>
                        <?= UserInfo::getUserGender($model->gender ?? $userInfo->gender); ?>
                    <?php endif; ?>
                </div>
                <div data-open-id="select-sex" class="open-select-field"></div>
            </div>
            <div id="select-sex" class="container-scroll auto-height">
                <div class="container-option-select option-active">
                    <?php if ($userInfo->isGenderNotSelected() && $model->isGenderNotSelected()): ?>
                        <div data-value="1" class="option-select-field">Мужской</div>
                        <div data-value="2" class="option-select-field">Женский</div>
                    <?php elseif ($userInfo->isUserMan() || $model->isUserMan()): ?>
                        <div data-value="2" class="option-select-field">Женский</div>
                    <?php else: ?>
                        <div data-value="1" class="option-select-field">Мужской</div>
                    <?php endif; ?>
                </div>
            </div>
            <?= $form->field($model, 'gender')
                ->hiddenInput(['id' => 'select-sex-hidden', 'value' => $model->gender ?? $userInfo->gender])
                ->label(false) ?>
        </div>
        <div class="block-field-setting">
            <label class="label-field-setting">Подключить аккаунты</label>
            <?php $authAuthChoice = AuthChoice::begin([
                'baseAuthUrl' => ['social-auth/auth'],
                'autoRender' => false,
            ]); ?>
            <?php foreach ($authAuthChoice->getClients() as $client): ?>
                <?php $activeBinding = SocialAuth::findBySource($socialBindings, $client->getName()) ?>
                <div class="icon-social icon-<?= $client->getName() ?>-30">
                    <?php if ($activeBinding): ?>
                        <a class="to-plug" target="_blank" data-pjax=0 href="<?= $activeBinding->createSocialUrl() ?>">
                            <?= ($activeBinding->screen_name !== '') ?
                                $activeBinding->screen_name : $activeBinding->source_id ?>
                        </a>
                    <?php else: ?>
                        <a class="to-plug" data-pjax=0 href="<?= $authAuthChoice->createClientUrl($client) ?>">Подключить</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <?php AuthChoice::end() ?>
        </div>
        <div class="btn-setting-save">
            <button class="large-wide-button" style="border: none;" type="submit"><p>Сохранить</p></button>
        </div>
    </div>

    <h1 class="h1-c center-mx" style="margin-top: 35px;">Почта и пароль</h1>
    <div class="container-settings" style="padding: 0px 20px;">
        <div class="block-field-setting">
            <label class="label-field-setting">Электронная почта</label>
            <div class="input-field-setting">
                <span class="сhange-email-btn"><?= $user->email ?></span>
            </div>
        </div>
        <?php if (!$user->hasSocialCreation() || $user->hasChangingPassword()): ?>
            <div class="block-field-setting">
                <label class="label-field-setting">Старый пароль</label>
                <?= $form->field($model, 'oldPassword')
                    ->passwordInput([
                        'style' => 'margin-bottom: 15px;',
                        'class' => 'input-field-setting',
                        'placeholder' => 'Введите пароль',
                        'value' => $model->oldPassword,
                    ])
                    ->label(false) ?>
            </div>
        <?php endif; ?>
        <div class="block-field-setting">
            <label class="label-field-setting">Новый пароль</label>
            <?= $form->field($model, 'newPassword')
                ->passwordInput([
                    'style' => 'margin-bottom: 15px;',
                    'class' => 'input-field-setting',
                    'placeholder' => 'Введите пароль',
                    'value' => $model->newPassword,
                ])
                ->label(false) ?>
        </div>
        <div class="block-field-setting">
            <label class="label-field-setting">Повторите пароль</label>
            <?= $form->field($model, 'newPasswordRepeat')
                ->passwordInput([
                    'style' => 'margin-bottom: 15px;',
                    'class' => 'input-field-setting',
                    'placeholder' => 'Еще раз новый пароль',
                    'value' => $model->newPasswordRepeat,
                ])
                ->label(false) ?>
        </div>
        <div class="btn-setting-save">
            <button class="large-wide-button" style="border: none;" type="submit"><p>Сохранить</p></button>
        </div>
    </div>

    <h1 class="h1-c center-mx" style="margin-top: 35px;">Уведомления по эл.почте</h1>
    <div class="container-settings" style="padding: 0px 20px;">

        <div class="block-field-setting">
            <label class="label-field-setting">Комментарии к моим отзывам</label>
            <div class="selected-field">
                <div id="answers-to-reviews-value" data-value="<?= $model->answersToReviews ??
                $userInfo->answers_to_reviews_sub ?>" class="select-value">
                    <?= UserInfo::getUserChoice($model->answersToReviews ??
                        $userInfo->answers_to_reviews_sub) ?>
                </div>
                <div data-open-id="answers-to-reviews" class="open-select-field"></div>
            </div>
            <div id="answers-to-reviews" class="container-scroll auto-height">
                <div class="container-option-select option-active">
                    <?php if ((bool)($model->answersToReviews ?? $userInfo->answers_to_reviews_sub)): ?>
                        <div data-value="0" class="option-select-field">Нет</div>
                    <?php else: ?>
                        <div data-value="1" class="option-select-field">Да</div>
                    <?php endif; ?>
                </div>
            </div>
            <?= $form->field($model, 'answersToReviews')
                ->hiddenInput([
                    'id' => 'answers-to-reviews-hidden',
                    'value' => $model->answersToReviews ??
                        $userInfo->answers_to_reviews_sub,
                ])->label(false) ?>
        </div>

        <div class="block-field-setting">
            <label class="label-field-setting">Ответы к моим комментариям</label>
            <div class="selected-field">
                <div id="answers-to-comments-value" data-value="<?= $model->answersToComments ??
                $userInfo->answers_to_comments_sub ?>" class="select-value">
                    <?= UserInfo::getUserChoice($model->answersToComments ??
                        $userInfo->answers_to_comments_sub) ?>
                </div>
                <div data-open-id="answers-to-comments" class="open-select-field"></div>
            </div>
            <div id="answers-to-comments" class="container-scroll auto-height">
                <div class="container-option-select option-active">
                    <?php if ((bool)($model->answersToComments ?? $userInfo->answers_to_comments_sub)): ?>
                        <div data-value="0" class="option-select-field">Нет</div>
                    <?php else: ?>
                        <div data-value="1" class="option-select-field">Да</div>
                    <?php endif; ?>
                </div>
            </div>
            <?= $form->field($model, 'answersToComments')
                ->hiddenInput([
                    'id' => 'answers-to-comments-hidden',
                    'value' => $model->answersToComments ??
                        $userInfo->answers_to_comments_sub,
                ])->label(false) ?>
        </div>

        <div class="block-field-setting">
            <label class="label-field-setting">Отзывы к моим местам</label>
            <div class="selected-field">
                <div id="reviews-to-my-places-value" data-value="<?= $model->reviewsToMyPlaces ??
                $userInfo->reviews_to_my_places_sub ?>" class="select-value">
                    <?= UserInfo::getUserChoice($model->reviewsToMyPlaces ??
                        $userInfo->reviews_to_my_places_sub) ?>
                </div>
                <div data-open-id="reviews-to-my-places" class="open-select-field"></div>
            </div>
            <div id="reviews-to-my-places" class="container-scroll auto-height">
                <div class="container-option-select option-active">
                    <?php if ((bool)($model->reviewsToMyPlaces ??
                        $userInfo->reviews_to_my_places_sub)): ?>
                        <div data-value="0" class="option-select-field">Нет</div>
                    <?php else: ?>
                        <div data-value="1" class="option-select-field">Да</div>
                    <?php endif; ?>
                </div>
            </div>
            <?= $form->field($model, 'reviewsToMyPlaces')
                ->hiddenInput([
                    'id' => 'reviews-to-my-places-hidden',
                    'value' => $model->reviewsToMyPlaces ?? $userInfo->reviews_to_my_places_sub,
                ])
                ->label(false) ?>
        </div>

        <div class="block-field-setting">
            <label class="label-field-setting">Отзывы к местам в Избранном</label>
            <div class="selected-field">
                <div id="reviews-to-favorite-places-value" data-value="<?= $model->reviewsToFavoritePlaces ??
                $userInfo->reviews_to_favorite_places_sub ?>" class="select-value">
                    <?= UserInfo::getUserChoice($model->reviewsToFavoritePlaces ??
                        $userInfo->reviews_to_favorite_places_sub) ?>
                </div>
                <div data-open-id="reviews-to-favorite-places" class="open-select-field"></div>
            </div>
            <div id="reviews-to-favorite-places" class="container-scroll auto-height">
                <div class="container-option-select option-active">
                    <?php if ((bool)($model->reviewsToFavoritePlaces ??
                        $userInfo->reviews_to_favorite_places_sub)): ?>
                        <div data-value="0" class="option-select-field">Нет</div>
                    <?php else: ?>
                        <div data-value="1" class="option-select-field">Да</div>
                    <?php endif; ?>
                </div>
            </div>
            <?= $form->field($model, 'reviewsToFavoritePlaces')
                ->hiddenInput([
                    'id' => 'reviews-to-favorite-places-hidden',
                    'value' => $model->reviewsToFavoritePlaces ?? $userInfo->reviews_to_favorite_places_sub,
                ])
                ->label(false) ?>
        </div>

        <div class="block-field-setting">
            <label class="label-field-setting">Начисление опыта и бонусов</label>
            <div class="selected-field">
                <div id="experience-and-bonus-value" data-value="<?= $model->experienceAndBonus ??
                $userInfo->experience_and_bonus_sub ?>" class="select-value">
                    <?= UserInfo::getUserChoice($model->experienceAndBonus ??
                        $userInfo->experience_and_bonus_sub) ?>
                </div>
                <div data-open-id="experience-and-bonus" class="open-select-field"></div>
            </div>
            <div id="experience-and-bonus" class="container-scroll auto-height">
                <div class="container-option-select option-active">
                    <?php if ((bool)($model->experienceAndBonus ??
                        $userInfo->experience_and_bonus_sub)): ?>
                        <div data-value="0" class="option-select-field">Нет</div>
                    <?php else: ?>
                        <div data-value="1" class="option-select-field">Да</div>
                    <?php endif; ?>
                </div>
            </div>
            <?= $form->field($model, 'experienceAndBonus')
                ->hiddenInput([
                    'id' => 'experience-and-bonus-hidden',
                    'value' => $model->experienceAndBonus ?? $userInfo->experience_and_bonus_sub,
                ])
                ->label(false) ?>
        </div>

        <div class="block-field-setting">
            <div class="block-field-setting">
                <label class="label-field-setting">Интересные подборки мест и скидок</label>
                <div class="selected-field">
                    <div id="places-and-discounts-value" data-value="<?= $model->placesAndDiscounts ??
                    $userInfo->places_and_discounts_sub ?>" class="select-value">
                        <?= UserInfo::getUserChoice($model->placesAndDiscounts ??
                            $userInfo->places_and_discounts_sub) ?>
                    </div>
                    <div data-open-id="places-and-discounts" class="open-select-field"></div>
                </div>
                <div id="places-and-discounts" class="container-scroll auto-height">
                    <div class="container-option-select option-active">
                        <?php if ((bool)($model->placesAndDiscounts ?? $userInfo->places_and_discounts_sub)): ?>
                            <div data-value="0" class="option-select-field">Нет</div>
                        <?php else: ?>
                            <div data-value="1" class="option-select-field">Да</div>
                        <?php endif; ?>
                    </div>
                </div>
                <?= $form->field($model, 'placesAndDiscounts')
                    ->hiddenInput([
                        'id' => 'places-and-discounts-hidden',
                        'value' => $model->placesAndDiscounts ?? $userInfo->places_and_discounts_sub,
                    ])
                    ->label(false) ?>
            </div>
        </div>

        <div class="btn-setting-save">
            <button class="large-wide-button" style="border: none;" type="submit"><p>Сохранить</p></button>
        </div>
        <?php ActiveForm::end() ?>
    </div>
</div>
<div style="margin-bottom:30px;"></div>

<?php
if (isset($toastMessage)) {
    $js = <<<JS
    $(document).ready(function () {
        $().toastmessage('showToast', {
            text     : '$toastMessage[message]',
            stayTime:         5000,
            type     : '$toastMessage[type]'
        });
    });
JS;
    echo "<script>$js</script>";
}
Pjax::end();
?>

