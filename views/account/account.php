<?php
use app\components\breadCrumb\BreadCrumb;
use \app\components\Helper;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;


$this->title = 'Пополнить счет на Postim.by';

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
    <h1 class="h1-v">Пополнение счета</h1>

</div>
<div class="block-flex-white">
    <div class="block-content">
        <div class="menu-btns-card">

            <a href="<?= Url::to(['account/replenishment'])?>" >
                <div class="btn2-menu active"><span class="under-line">Пополнить</span></div>
            </a>
            <a href="<?= Url::to(['account/history'])?>">
                <div class="btn2-menu "><span class="under-line">История</span></div>
            </a>
        </div>
    </div>
</div>

<div class="block-content">
    <div class="std-container">
        <form method="post" id="account-form">
            <div class="fill-account-header">
                На счету: <?= $userInfo->virtual_money?> руб и <?= $userInfo->mega_money?> мега-руб
            </div>
            <div class="fill-account-content">
                <div style="margin-bottom: 35px;">
                    <span>Сумма</span>
                    <input id="payment-form-money" type="text" class="custom-text-input" name="payment[money]"
                           style="display: inline-block; width: 159px; margin: 0 10px;" placeholder="Укажите сумму" value="<?= $model->money?>">
                    <span>руб</span>
                </div>
                <div class="fill-account-text">Выберите способ оплаты</div>
                <div class="payment-methods">
                    <div class="payment-block">
                        <div class="erip make-payment" data-type="1">
                            <span>Система "Расчет"<br>(ЕРИП)</span>
                        </div>
                        <div class="bank disable" data-type="2" title="Временно недоступно">
                            <span>Банковская<br>карта</span>
                        </div>
                    </div>
                    <input id="payment-form-type" type="hidden" name="payment[type]">
                </div>
                <div class="fill-account-agreement">
                    Пополняя счет, вы соглашаетесь cо всеми правилами
                    и условиями <a href="/agreement">пользовательского соглашения</a> на 100%.<br>
                    Если у вас возникли вопросы по оплате, ознакомьтесь с инструкцией.
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#payment-form-money').mask("###0.00", {reverse: true});

        <?php if (isset($errors[0])):?>
            $().toastmessage('showToast', {
                text: '<?=$errors[0]?>',
                stayTime: 5000,
                type: 'error'
            });
        <?php endif;?>
    });
</script>
<?php
Pjax::end();
?>
