<?php
use app\components\breadCrumb\BreadCrumb;
use \app\components\Helper;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;


$this->title = 'Покупка промокода на Postim.by';
?>

<div class="block-content" style="margin-top: 80px">
    <div class="bread-crumb">
        <a class="pre" href="#">Главная</a>
        <span class="separator"></span>
        <p>Покупка промокода</p>
    </div>
    <h1 class="h1-v">Покупка промокода</h1>

</div>


<div class="block-content">
    <div class="std-container" style="margin-top: 25px;">
        <form id="discount-order-form" method="post">
            <div class="discount-order-content">
                <div class="order-info" style="margin-bottom: 15px;">
                    <div class="product-picture" style="background-image: url('/testP.png')"></div>
                    <div class="product-info">
                        <div class="product-header"><?=$discount->header?></div>
                        <div class="product-select-count">
                            <div style="margin-right: 9px;">Колличество</div>
                            <div class="product-counter">
                                <div class="remove"></div>
                                <input type="text" name="discountOrder[count]" class="counter" value="1" readonly>
                                <div class="add"></div>
                            </div>
                        </div>
                        <div class="product-total-cost">
                            Сумма
                            <span class="cost">
                                <span id="total-cost" data-start-value="<?=$discount->price_promo?>">
                                    <?=$discount->price_promo?>
                                </span> руб.
                            </span>
                        </div>
                        <div class="btn-order-discount" style="margin-top: 22px;">
                            Заказать
                        </div>
                    </div>
                </div>

                <div class="fill-account-text">Выберите способ оплаты</div>
                <div class="payment-methods" style="margin-bottom: 0;">
                    <div class="payment-block">
                        <div class="erip disable" data-payment-type="erip">
                            <span>Система "Расчет"<br>(ЕРИП)</span>
                        </div>
                        <div class="bank disable" data-payment-type="card">
                            <span>Банковская<br>карта</span>
                        </div>
                    </div>
                    <div class="payment-block">
                        <div class="purse disable" data-payment-type="purse">
                            <span>Кошелек<br>(рубли)</span>
                        </div>
                        <div class="purse selected" data-payment-type="m-purse">
                            <span>Кошелек<br>(мега-рубли)</span>
                        </div>
                    </div>
                    <input type="hidden" name="discountOrder[paymentType]" value="m-purse">
                </div>
            </div>
        </form>

    </div>


</div>
