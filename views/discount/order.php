<?php
use app\components\breadCrumb\BreadCrumb;

$this->title = 'Покупка промокода на Postim.by';
?>

<div class="block-content" style="margin-top: 80px">
    <?=BreadCrumb::widget(['breadcrumbParams'=>$breadcrumbParams])?>
    <h1 class="h1-v">Покупка промокода</h1>

</div>


<div class="block-content">
    <div class="std-container" style="margin-top: 25px;">
        <form id="discount-order-form" method="post">
            <div class="discount-order-content">
                <div class="order-info" style="margin-bottom: 15px;">
                    <div class="product-picture" style="background-image: url('<?=$discount->getCover();?>')"></div>
                    <div class="product-info">
                        <div class="product-header"><?=$discount->header?></div>
                        <div class="product-select-count">
                            <div style="margin-right: 9px;">Колличество</div>
                            <div class="product-counter">
                                <div class="remove"></div>
                                <div class="counter">1</div>
                                <div class="add"></div>
                            </div>
                        </div>
                        <div class="product-total-cost">
                            Сумма
                            <span class="cost">
                                <span id="total-cost" data-start-value="">

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
                        <div class="erip disable" data-payment-type="1">
                            <span>Система "Расчет"<br>(ЕРИП)</span>
                        </div>
                        <div class="bank disable" data-payment-type="2">
                            <span>Банковская<br>карта</span>
                        </div>
                    </div>
                    <div class="payment-block">
                        <div class="purse disable" data-payment-type="3">
                            <span>Кошелек<br>(рубли)</span>
                        </div>
                        <div class="purse selected" data-payment-type="4">
                            <span>Кошелек<br>(мега-рубли)</span>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="discount-order-count" name="discountOrder[count]" value="1">
                <input type="hidden" name="discountOrder[discountType]" value="<?=$discount->type?>">
                <input type="hidden" id="discount-order-paymentType" name="discountOrder[paymentType]" value="4">
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
    })
</script>
