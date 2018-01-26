<?php
use app\components\breadCrumb\BreadCrumb;
use \app\components\Helper;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;


$this->title = 'Премиум аккаунт на Postim.by';
$hasOnePost = count($posts) === 1;

Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => true,
    'id' => 'account-feeds',
    'linkSelector' => '#account-feeds .menu-btns-card a',
    'formSelector' => false,
])
?>
<div class="block-content" style="margin-top: 80px">
    <?= BreadCrumb::widget(['breadcrumbParams'=>$breadcrumbParams])?>
    <h1 class="h1-v">Премиум аккаунт</h1>

</div>
<div class="block-content" style="margin-top: 30px;">
    <div class="std-container">
        <form method="post" id="premium-account-form">
            <div class="container-add-place">
                <div class="block-field-setting">
                    <label class="label-field-setting">Название</label>
                    <div class="selected-field">
                        <div id="select-place-value" data-value="" class="select-value">
                            <?php if ($hasOnePost):?>
                                <?=$posts[0]->data . ' (' . $posts[0]->address . ')'?>
                            <?php else:?>
                                <span class="placeholder-select">Выберите название</span>
                            <?php endif?>
                        </div>
                        <div data-open-id="select-place" class="open-select-field"></div>
                    </div>
                    <div id="select-place" class="container-scroll auto-height">
                        <div class="container-option-select option-active">
                            <?php if (!$hasOnePost):?>
                                <?php foreach ($posts as $post):?>
                                    <div data-value="<?=$post->id?>" class="option-select-field">
                                        <?=$post->data . ' (' . $post->address . ')'?>
                                    </div>
                                <?php endforeach?>
                            <?php endif?>
                        </div>
                    </div>
                    <input type="hidden" id="select-place-hidden" name="premium-account[postId]"
                           value="<?= $hasOnePost ? $posts[0]->id : ''?>">
                </div>


                <div class="block-field-setting">
                    <label class="label-field-setting" style="margin-bottom: 10px;">Выберите период</label>
                    <input type="radio" name="premium-account[rate]"
                           class="style-checkbox-chbox" id="custom-checkbox1" value="1">
                    <label for="custom-checkbox1" class="custom-checkbox-label">
                        <div>
                            30 руб. / 30 дней
                            <div class="economy-text">Без экономии</div>
                        </div>
                    </label>
                    <input type="radio" name="premium-account[rate]"
                           class="style-checkbox-chbox" id="custom-checkbox2" value="2">
                    <label for="custom-checkbox2" class="custom-checkbox-label">
                        <div>
                            79 руб. / 90 дней
                            <div class="economy-text">Экономия 9 руб.</div>
                        </div>
                    </label>
                    <input type="radio" name="premium-account[rate]"
                           class="style-checkbox-chbox" id="custom-checkbox3" value="3">
                    <label for="custom-checkbox3" class="custom-checkbox-label">
                        <div>
                            149 руб. / 180 дней
                            <div class="economy-text">Экономия 31 руб.</div>
                        </div>
                    </label>
                    <input type="radio" name="premium-account[rate]"
                           class="style-checkbox-chbox" id="custom-checkbox4" value="4" checked>
                    <label for="custom-checkbox4" class="custom-checkbox-label">
                        <div>
                            259 руб. / 365 дней
                            <div class="economy-text">Экономия 106 руб.</div>
                        </div>
                    </label>
                    <button class="btn-custom" style="margin: 10px 0 20px 0;" type="submit">
                        Подключить
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {

        <?php if (isset($errors[0])):?>
            $().toastmessage('showToast', {
                text: '<?=$errors[0]?>',
                stayTime: 5000,
                type: 'error'
            });
        <?php endif;?>

        <?php if ($message = Yii::$app->session->getFlash('message')):?>
            $().toastmessage('showToast', {
                text: '<?=$message['text']?>',
                stayTime: 8000,
                type: '<?=$message['type']?>'
            });
        <?php endif;?>
    });
</script>
<?php
Pjax::end();
?>
