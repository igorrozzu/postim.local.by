<?php
use app\models\SocialAuth;
use yii\authclient\widgets\AuthChoice;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\widgets\ActiveForm;

$this->registerJsFile('/js/user-settings.js', ['position' => View::POS_END]);
$user = Yii::$app->user->identity;
?>

<div class="margin-top60"></div>
<div class="block-content">
    <h1 class="h1-c center-mx" style="margin-top: 35px;">Персональные данные</h1>
    <div class="container-settings">
        <div class="user-icon-profile"><img src="<?=$user->getPhoto()?>"></div>
        <label class="btn-add-icon" for="user-photo">Загрузить фото</label>
        <input type="file" name="user-photo" id="user-photo" style="display: none;">
        <div id="user-photo-uploading-error" class="help-block"></div>
        <div class="block-field-setting">
            <label class="label-field-setting">Имя</label>
            <input class="input-field-setting" placeholder="Введите имя" value="">
        </div>
        <div class="block-field-setting">
            <label class="label-field-setting">Фамилия</label>
            <input class="input-field-setting" placeholder="Введите фамилию" value="">
        </div>
        <div class="block-field-setting">
            <label class="label-field-setting">Город</label>
            <div class="selected-field">
                <div id="select-city-value" data-value="" class="select-value"><span class="placeholder-select">Выберите город</span></div>
                <div data-open-id="select-city" class="open-select-field"></div>
                <input type="hidden" name="select-city">
            </div>
            <div id="select-city" class="container-scroll container-scroll-active">
                <div class="container-option-select option-active">
                    <div data-value="Минск" class="option-select-field">Минск</div>
                    <div data-value="Брест" class="option-select-field">Брест</div>
                    <div data-value="Витебск" class="option-select-field">Витебск</div>
                    <div data-value="Гродно" class="option-select-field">Гродно</div>
                    <div data-value="Гомель" class="option-select-field">Гомель</div>
                    <div data-value="Березено" class="option-select-field">Березено</div>
                    <div data-value="Борисов" class="option-select-field">Борисов</div>
                    <div data-value="Волковыск" class="option-select-field">Волковыск</div>
                    <div data-value="Жодино" class="option-select-field">Жодино</div>
                    <div data-value="Крупки" class="option-select-field">Крупки</div>
                    <div data-value="Могилев" class="option-select-field">Могилев</div>
                    <div data-value="Барановичи" class="option-select-field">Барановичи</div>
                </div>
            </div>
        </div>
        <div class="block-field-setting">
            <label class="label-field-setting">Пол</label>
            <div class="selected-field">
                <div id="select-sex-value" data-value="" class="select-value"><span class="placeholder-select">Выберите пол</span></div>
                <div data-open-id="select-sex" class="open-select-field"></div>
                <input type="hidden" name="select-sex">
            </div>
            <div id="select-sex" class="container-scroll auto-height">
                <div class="container-option-select option-active">
                    <div data-value="1" class="option-select-field">Мужской</div>
                    <div data-value="2" class="option-select-field">Женский</div>
                </div>
            </div>
        </div>
        <div class="block-field-setting">
            <label class="label-field-setting">Подключить аккаунты</label>
            <?php $authAuthChoice = AuthChoice::begin([
                'baseAuthUrl' => ['social-auth/auth']
            ]); ?>
            <?php foreach ($authAuthChoice->getClients() as $client): ?>
                <?php $activeBinding = SocialAuth::findBySource($socialBindings, $client->getName())?>
                <div class="icon-social icon-<?=$client->getName()?>-30">
                <?php if($activeBinding): ?>
                    <a class="to-plug" target="_blank" href="<?=$activeBinding->createSocialUrl()?>">
                        <?=($activeBinding->screen_name !== '') ?
                            $activeBinding->screen_name : $activeBinding->source_id?>
                    </a>
                <?php else: ?>
                    <a class="to-plug" href="<?= $authAuthChoice->createClientUrl($client) ?>">Подключить</a>
                <?php endif;?>
                </div>
            <?php endforeach; ?>
            </div> <!-- <--Закрывает блок виджета, не удалять!</div>-->
        </div>
    </div>
    <h1 class="h1-c center-mx" style="margin-top: 35px;">Почта и пароль</h1>
    <div class="container-settings" style="padding: 0px 20px;">
        <div class="block-field-setting">
            <label class="label-field-setting">Электронная почта</label>
            <input class="input-field-setting" placeholder="Введите email (Выслать письмо активации)" value="">
        </div>
        <div class="block-field-setting">
            <label class="label-field-setting">Старый пароль</label>
            <input class="input-field-setting" placeholder="Введите пароль" value="">
        </div>
        <div class="block-field-setting">
            <label class="label-field-setting">Новый пароль</label>
            <input class="input-field-setting" placeholder="Введите пароль" value="">
        </div>
        <div class="block-field-setting">
            <label class="label-field-setting">Повторите пароль</label>
            <input class="input-field-setting" placeholder="Еще раз новый пароль" value="">
        </div>
    </div>
    <h1 class="h1-c center-mx" style="margin-top: 35px;">Уведомления по эл.почте</h1>
    <div class="container-settings" style="padding: 0px 20px;">
        <div class="block-field-setting">
            <label class="label-field-setting">Ответы к моим отзывам</label>
            <div class="selected-field">
                <div id="answers-to-reviews-value" data-value="1" class="select-value">Да</div>
                <div data-open-id="answers-to-reviews" class="open-select-field"></div>
                <input type="hidden" name="answers-to-reviews">
            </div>
            <div id="answers-to-reviews" class="container-scroll auto-height">
                <div class="container-option-select option-active">
                    <div data-value="2" class="option-select-field">Нет</div>
                </div>
            </div>
        </div>
        <div class="block-field-setting">
            <label class="label-field-setting">Ответы к моим комментариям</label>
            <div class="selected-field">
                <div id="answers-to-comments-value" data-value="1" class="select-value">Да</div>
                <div data-open-id="answers-to-comments" class="open-select-field"></div>
                <input type="hidden" name="answers-to-comments">
            </div>
            <div id="answers-to-comments" class="container-scroll auto-height">
                <div class="container-option-select option-active">
                    <div data-value="2" class="option-select-field">Нет</div>
                </div>
            </div>
        </div>
        <div class="block-field-setting">
            <label class="label-field-setting">Отзывы и комментарии к моим местам</label>
            <div class="selected-field">
                <div id="reviews-and-comments-to-places-value" data-value="1" class="select-value">Да</div>
                <div data-open-id="reviews-and-comments-to-places" class="open-select-field"></div>
                <input type="hidden" name="reviews-and-comments-to-places">
            </div>
            <div id="reviews-and-comments-to-places" class="container-scroll auto-height">
                <div class="container-option-select option-active">
                    <div data-value="2" class="option-select-field">Нет</div>
                </div>
            </div>
        </div>
        <div class="block-field-setting">
            <div class="block-field-setting">
                <label class="label-field-setting">Интересные подборки мест и скидок</label>
                <div class="selected-field">
                    <div id="places-and-discounts-value" data-value="1" class="select-value">Да</div>
                    <div data-open-id="places-and-discounts" class="open-select-field"></div>
                    <input type="hidden" name="places-and-discounts">
                </div>
                <div id="places-and-discounts" class="container-scroll auto-height">
                    <div class="container-option-select option-active">
                        <div data-value="2" class="option-select-field">Нет</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="btn-setting-save">
            <div class="large-wide-button"><p>Сохранить</p></div>
        </div>
    </div>
</div>
<div style="margin-bottom:30px;"></div>