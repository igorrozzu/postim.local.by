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
    <div class="bread-crumb">
        <a class="pre" href="#">Главная</a>
        <span class="separator"></span>
        <p>История вашего счета</p>
    </div>
    <h1 class="h1-v">История вашего счета</h1>

</div>
<div class="block-flex-white">
    <div class="block-content">
        <div class="menu-btns-card">

            <a href="#" >
                <div class="btn2-menu"><span class="under-line">Пополнить</span></div>
            </a>
            <a href="#">
                <div class="btn2-menu active"><span class="under-line">История</span></div>
            </a>
        </div>
    </div>
</div>

<div class="block-content">
    <div class="std-container" style="box-shadow: none">
        <div class="fill-account-header">
            На счету: 00,0 руб и 12,5 мега-руб
        </div>
        <div class="filter-menu">
            <a href="#" >
                <div class="filter-menu-btn active"><span class="under-line">Все</span></div>
            </a>
            <a href="#">
                <div class="filter-menu-btn"><span class="under-line">Платежи</span></div>
            </a>
            <a href="#">
                <div class="filter-menu-btn"><span class="under-line">Бонусы</span></div>
            </a>
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
            <div class="large-wide-button non-border btn-load-more" id="" style="height: 50px;"
                 data-selector_replace="#"
                 data-href="">
                <p>Показать больше</p>
            </div>
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
