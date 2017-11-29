<?php
use app\components\breadCrumb\BreadCrumb;
use \app\components\Helper;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;


$this->title = 'Покупка сертификата на Postim.by';
?>

<div class="block-content" style="margin-top: 80px">
    <div class="bread-crumb">
        <a class="pre" href="#">Главная</a>
        <span class="separator"></span>
        <p>Покупка сертификата</p>
    </div>
    <h1 class="h1-v">Покупка сертификата</h1>

</div>


<div class="block-content">
    <div class="std-container" style="margin-top: 25px;">

        <div class="discount-order-content">
            <div class="order-info" style="margin-bottom: 15px;">
                <div class="product-picture" style="background-image: url('/testP.png')"></div>
                <div class="product-info">
                    <div class="product-header">Скидка 50% на букеты из конфет и игрушек. Букет из мужских носков за 25 руб.</div>
                    <div class="product-select-count">
                        <div style="margin-right: 9px;">Колличество</div>
                        <div class="product-counter">
                            <div class="remove"></div>
                            <div class="counter">1</div>
                            <div class="add"></div>
                        </div>
                    </div>
                    <div class="product-total-cost">
                        Сумма <span class="cost">1.50 руб.</span>
                    </div>
                </div>
            </div>

            <div class="fill-account-text">Выберите способ оплаты</div>
            <div class="payment-methods" style="margin-bottom: 0;">
                <div class="erip">
                    <span>Система "Расчет"<br>(ЕРИП)</span>
                </div>
                <div class="bank">
                    <span>Банковская<br>карта</span>
                </div>
                <div class="purse">
                    <span>Кошелек<br>(рубли)</span>
                </div>
                <div class="purse">
                    <span>Кошелек<br>(мега-рубли)</span>
                </div>
            </div>
        </div>

    </div>


</div>
