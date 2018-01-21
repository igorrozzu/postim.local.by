<?php
use app\components\breadCrumb\BreadCrumb;
use \app\components\Helper;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;


$this->title = 'История вашего счета';

Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => true,
    'id' => 'account-feeds',
    'linkSelector' => '#account-feeds .menu-btns-card a',
    'formSelector' => false,
])
?>
<div class="block-content" style="margin-top: 80px;">
    <?= BreadCrumb::widget(['breadcrumbParams'=>$breadcrumbParams])?>
    <h1 class="h1-v">История вашего счета</h1>

</div>
<div class="block-flex-white">
    <div class="block-content">
        <div class="menu-btns-card">

            <a href="<?= Url::to(['account/replenishment'])?>" >
                <div class="btn2-menu"><span class="under-line">Пополнить</span></div>
            </a>
            <a href="<?= Url::to(['account/history'])?>">
                <div class="btn2-menu active"><span class="under-line">История</span></div>
            </a>
        </div>
    </div>
</div>

<div class="block-content">
    <div class="std-container" style="box-shadow: none">
        <div class="fill-account-header">
            На счету: 00,0 руб
        </div>

        <div class="statistic-content">
            <table class="statistic-table">
                <tr>
                    <td>02.02.2017</td>
                    <td class="money-minus">-1,10 мега-руб</td>
                    <td>Ужасное обслуживание! Я заказала заказ, но его так долго готовили! И это даже не выходной день! решили выпить чаю, выбрали и заказали, но официант сказал, что такого уже нет, заказали еще, снова та же ситуация.. с третьего раза заказали то, что у них было. На вид бургеры не плохо смотрятся, но на вкус... один бургер с рыбой, а другой с курицей.. знаете, я пожалела, что после работы зашла в это заведение... официант принес бургеры, а про приборы забыл, пришлось попросить. После мы решили не заказывать десерты, мне хотелось просто уйти.. Я попросила счет. Официант сказал, что сейчас принесет и пропал. Спустя минут30, мой спутник попросил повторно счет... Единственное, что мне понравился, так это чай, но еда оставляет желать лучшего..</td>
                </tr>
                <tr>
                    <td>02.02.2017</td>
                    <td class="money-plus">+1,10 руб</td>
                    <td>02.02.201702.02702.02 .201702.02.201702.02.201702.02.2017</td>
                </tr>
            </table>
        </div>
        <div class="large-wide-button non-border btn-load-more" id="" style="height: 50px;"
             data-selector_replace="#"
             data-href="">
            <p>Показать больше</p>
        </div>



    </div>


</div>

<script>
    $(document).ready(function () {
        main.initCustomScrollBar($('.statistic-content'),{axis: "x",scrollInertia: 50})
    });
</script>

<?php
Pjax::end();
?>
