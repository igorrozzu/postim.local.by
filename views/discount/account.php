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
<div class="block-content" style="margin-top: 100px">
    <div class="bread-crumb">
        <a class="pre" href="#">Главная</a>
        <span class="separator"></span>
        <p>Пополнение счета</p>
    </div>
    <h1 class="h1-v">Пополнение счета</h1>

</div>
<div class="block-flex-white">
    <div class="block-content">
        <div class="menu-btns-card">

            <a href="#" >
                <div class="btn2-menu active"><span class="under-line">Пополнить</span></div>
            </a>
            <a href="#">
                <div class="btn2-menu "><span class="under-line">История</span></div>
            </a>
        </div>
    </div>
</div>

<div class="block-content">
    <div class="std-container">
        <div class="fill-account-header">
            На счету: 00,0 руб и 12,5 мега-руб
        </div>
        <div class="fill-account-content">
            <div style="margin-bottom: 35px;">
                <span>Сумма</span>
                <input type="text" class="custom-text-input"
                       style="display: inline-block; width: 180px; margin: 0 10px;"
                       name="money" placeholder="Укажите сумму">
                <span>руб</span>
            </div>
            <div class="fill-account-text">Выберите способ оплаты</div>
            <div class="payment-methods">
                <div class="erip">
                    <span>Система "Расчет"<br>(ЕРИП)</span>
                </div>
                <div class="bank">
                    <span>Банковская<br>карта</span>
                </div>
            </div>
            <div class="fill-account-agreement">
                Пополняя счет, вы соглашаетесь во всеми правилами<br>
                и условиями пользовательского соглашения на 100%.<br>
                Если у вас возникли вопросы по оплате,<br>
                ознакомьтесь с инструкцией.
            </div>
        </div>

    </div>


</div>


<?php
Pjax::end();
?>
