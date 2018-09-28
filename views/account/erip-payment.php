<?php


$this->title = 'Оплата через систему "Расчет" (ЕРИП)';

?>
<div class="block-content" style="margin-top: 80px">
    <div class="bread-crumb">
        <a class="pre" href="#">Главная</a>
        <span class="separator"></span>
        <p>Оплата через систему "Расчет" (ЕРИП)</p>
    </div>
    <h1 class="h1-v">Оплата через систему "Расчет" (ЕРИП)</h1>

</div>

<div class="block-content">
    <div class="std-container">
        <div class="fill-account-content" style="margin-top: 30px;">
            <table style="margin-bottom: 20px;">
                <tr>
                    <td><span class="fill-account-text style1">Заказ №</span></td>
                    <td style="padding: 0 0 10px 10px;">
                        <input type="text" class="payment-text-input" value="<?= $model->id ?>" readonly>
                    </td>
                </tr>
                <tr>
                    <td><span class="fill-account-text style1">Стоимость</span></td>
                    <td style="padding: 0 0 0 10px;">
                        <input type="text" class="payment-text-input" value="<?= $model->money ?>" readonly>
                    </td>
                </tr>
            </table>
            <div class="account-text" style="margin-bottom: 20px;">
                Номер заказа - понадобится Вам для оплаты
            </div>

            <div class="fill-account-text" style="margin-bottom: 20px;">Как оплатить</div>

            <div class="erip-payment" style="margin-bottom: 20px;">
                <span class="erip">Система "Расчет"<br>(ЕРИП)</span>
            </div>

            <div class="account-text" style="margin-bottom: 20px;">
                - электронными деньгами (Webmoney, Easypay, iPay, BelQI);<br>
                - платежной карточкой (интернет-банк, мобильный-банк, банкомат, инфокиоск, касса банка*);<br>
                - наличными (устройство cash-in, касса банка*).<br>
            </div>

            <div class="account-text" style="font-size: 14px; margin-bottom: 20px;">
                *если Вы осуществляете платеж в кассе банка, пожалуйста, сообщите кассиру о необходимости проведения<br>
                оплаты услуги «Postim.by» через систему «Расчет» (ЕРИП) и сообщите ему номер заказа.
            </div>

            <div class="account-text" style="margin-bottom: 20px;">
                Для оплаты необходимо:<br>
                1.Выбрать последовательно пункты:<br>
                &ensp;+Система «Расчет» (ЕРИП)<br>
                &ensp;&ensp;+Интернет-магазины/сервисы<br>
                &ensp;&ensp;&ensp;+Р<br>
                &ensp;&ensp;&ensp;&ensp;Postim.by<br>
                2. Ввести номер заказа<br>
                3. Проверить корректность информации<br>
                4. Совершить платеж<br>
            </div>

            <div class="account-text">
                Нахождение услуги в дереве системы «Расчет» (ЕРИП) в некоторых пунктах оплаты может<br>
                отличаться от описанного выше. В связи с этим, в случае возникновения проблем с поиском услуги,<br>
                предлагаем выполнить поиск по УНП: 591251086
            </div>
        </div>
    </div>
</div>

