<?php
use app\components\breadCrumb\BreadCrumb;
use \app\components\Helper;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

$this->title = 'Суши-сеты от 17,20 руб./684 г, роллы от 8 руб. от службы доставки "Суши Наши" на Postim.by';

$this->registerMetaTag([
    'name' => 'description',
    'content' => 'Суши-сеты от 17,20 руб./684 г, роллы от 8 руб. от службы доставки "Суши Наши" на Postim.by'
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content'=> 'Суши-сеты роллы Суши Наши'
]);
?>
<div class="margin-top60"></div>
<div id="map_block" class="block-map preload-map">
    <div class="btns-map">
        <div class="action-map" title="Открыть карту"></div>
        <div class="find-me" title="Найти меня"></div>
        <div class="zoom-plus"></div>
        <div class="zoom-minus"></div>
    </div>

    <div id="map" style="display: none"></div>
</div>

<div class="block-content">

    <h1 class="h1-v">Суши-сеты от 17,20 руб./684 г, роллы от 8 руб. от службы доставки "Суши Наши"</h1>
</div>
<div class="block-content">
    <div class="container-discount">
        <div class="container-discount-photos">
            <div class="discount-photos" style="background-image: url('/test-discount/header.png')">
                <div class="container-photos-inside">
                    <div class="pre-photo"></div>
                    <div class="next-photo"></div>
                </div>
            </div>
        </div>
        <div class="container-discount-info">
            <div class="discount-info-time-left">Акция действует до 30.12.17</div>
            <div class="discount-info-text">Стоимость<span class="discount-info-bold-text">17.20 руб</span></div>
            <div class="discount-info-text">Скидка<span class="discount-info-bold-text">до 66%</span></div>
            <div class="discount-info-text before-icon-user">Взяли<span class="discount-info-bold-text">0 промокодов</span></div>
            <div class="discount-info-text before-icon-purse">Цена<span class="discount-info-bold-text">бесплатно</span></div>
            <div class="container-bottom-btn">
                <div class="blue-btn-40 js-gain-promo"><p>Получить скидку 66%</p></div>
            </div>
        </div>
    </div>
    <h2 class="h2-c">Условия</h2>
    <div class="block-description-card">
        <ul>
            <li><span>С промокодом вы получаете скидку до 66% на суши-сеты. Воспользоваться скидкой вы можете до 31.12.2017.</span></li>
            <li><span>Расчет банковской картой возможен только при условии, что при заказе Вы указали. что желаете рассчитаться в безналичном расчете.</span></li>
            <li><span>Необходимо предъявлять промокод до заказа. Вы можете его назвать по телефону.</span></li>
            <li><span>Акция распространяется на заказы на вынос. с доставкой и в кафе.</span></li>
            <li><span>Минимальная сумма заказа на доставку: 18 руб.</span></li>
            <li><span>Стоимость доставки в пределах МКАД (включая Уручье): 3 руб.</span></li>
            <li><span>При заказе от 30 руб. доставка в пределах МКАД (включая Уручье) бесплатно.</span></li>
            <li><span>Доставка за пределы МКАД (Тарасово, Ждановичи, Копище, Боровая. пос. Боровляны, Валерьяново, Б.Стиклево. Шабаны, Колядичи, Сенница, Юбилейный. Щoмыслица) стоимость:</span></li>
            <li><span>Доставка за пределы МКАД (дальше указанных зон) осуществляется при минимальной сумме заказа 50 руб. (согласовывается с администратором).</span></li>
            <li><span>Поставщик несет полную ответственность перед потребителем за достоверность информации.</span></li>
            <li><span>Телефоны:<br>
            (033) 365-11-16 (MTS)<br>
            (025) 665-11-16 (Life)<br>
            (029) 361-11-16 (Velcom)<br>
            (029) 361-11-16 (Viber, WhatsApp)
                </span></li>
        </ul>
    </div>

    <h2 class="h2-c">Описание акции</h2>
    <div class="block-description-card">
        <div style="color: #444444;font-family: PT_Sans bold;">
            Сет "Четвертый" (684 г) 17,20 руб. вместо 38,80 руб.
        </div>
        Вулкан (сыр, огурец, тунец, тобико, соус спайси)<br>
        Норвегия (сом жареный, авокадо, огурец, стружка тунца)<br>
        Филадельфия Классик (сыр, лосось, авокадо)<br>
        <img src="/test-discount/pic1.png" width="300" style="margin: 10px 0;"><br>

        <div style="color: #444444;font-family: PT_Sans bold;">
             Сет "Кумото" (1043 г) 18,90 руб. вместо 42,80 руб.
        </div>
        "Тихий Океан" 180 г - (авокадо, крабовая паста, кунжут белый и чёрный)<br>
        "Шри-Ланка" 188 г - (сливочный сыр, чука салат, соус ореховый)<br>
        "Спайси угорь" 183 г - (сыр сливочный, угорь жареный, кунжут, авокадо и соус Спайси)<br>
        "Шри-Ланка" - 188 г (сыр сливочный, салат чука)<br>
        "Техас" - 198 г (сыр сливочный, перец сладкий, лосось копченый)<br>
        "Три рыбки" - 193 г (сыр сливочный, креветка, лосось свежий, угорь жаренный)<br>
        "Мясной двор" 225 г -( сыр сливочный, перец сладкий, огурец, авокадо и соус Спайси<br>
        с лососем и сыром 110 г - (сыр сливочный, лосось) с огурцом 110 г<br>
        <img src="/test-discount/header.png" width="300" style="margin: 10px 0;"><br>

        <div style="color: #444444;font-family: PT_Sans bold;">
            Сет "Аки" (1020 гр): 20,80 руб. вместо 46,90 руб.
        </div>
        "Шри-Ланка" - 188 г (сыр сливочный, салат чука)<br>
        "Техас" - 198 г (сыр сливочный, перец сладкий, лосось копченый)<br>
        "Три рыбки" - 193 г (сыр сливочный, креветка, лосось свежий, угорь жаренный)<br>
        "Веган шампиньон" - 183 г (шампиньоны жаренные, соус спайси)<br>
        "Лосось с авокадо" - 198 г (лосось, авокадо)<br>
        <img src="/test-discount/pic2.jpg" width="300" style="margin: 10px 0;"><br>

        <div style="color: #444444;font-family: PT_Sans bold;">
            Сет "Теплый" (800 г) 19 руб. вместо 33,70 руб.
        </div>
        Теплый ролл "Суши Наши" - 280 г (сыр сливочный, лосось, манго,икра Тобико оранжево-черная, соус спайси)<br>
        Теплый ролл "Лосось и Авокадо" - 260 г (сыр сливочный, лосось и авокадо)<br>
        Теплый ролл "Окунь и банан" - 260 г (сыр сливочный, окунь, банан, икра Тобико)<br>
        <img src="/test-discount/pic4.jpg" width="300" style="margin: 10px 0;"><br>

        <div style="color: #444444;font-family: PT_Sans bold;">
            Сет "Праздничный" (1141 г) 31,20 руб. вместо 68,80 руб.
        </div>
        Зеландия (сыр сливочный, лосось, чука салат, соус ореховый),<br>
        Бонито (сыр сливочный, лосось жареный, авокадо, стружка тунца),<br>
        Гейша (сыр сливочный, икра Тобико, тунец, лосось подкопченый),<br>
        Филадельфия Унаги (сыр сливочный, лосось, огурец, соус Унаги),<br>
        Спайси Угорь (сыр сливочный, угорь жареный, кунжут, соус Спайси.)<br>
        <img src="/test-discount/pic5.jpg" width="300" style="margin: 10px 0;"><br>

        <div style="color: #444444;font-family: PT_Sans bold;">
            Сет "Камикадзе" (1068 г) 27,60 руб. вместо 68,90 руб.
        </div>
        Камикадзе (лосось, салат, соус Спайси)<br>
        Спайси Угорь (сыр, угорь, авокадо, кунжут, соус Спайси)<br>
        Пекин (сыр,тунец, авокадо, огурец, соус Спайси)<br>
        Зеландия (сыр, лосось, чука)<br>
        Филадельфия Классик (сыр, лосось, авокадо)<br>
        <img src="/test-discount/pic6.jpg" width="300" style="margin: 10px 0;"><br>

        <div style="color: #444444;font-family: PT_Sans bold;">
            Сет "Самурай" (970 г) 26,90 руб. вместо 59,60 руб.
        </div>
        Самурай (сыр, креветка, салат свежий, Тобико)<br>
        Филадельфия Киви (лосось, сыр, киви)<br>
        Тибет (угорь, сыр, огурец, авокадо)<br>
        Лосось с авокадо (лосось, авокадо)<br>
        Нигири с опаленным тунцом, Нигири с лососем<br>
        <img src="/test-discount/pic7.jpg" width="300" style="margin: 10px 0;"><br>

        <div style="color: #444444;font-family: PT_Sans bold;">
            Сет "Киото" (1191 г) 29,80 руб. вместо 87,80 руб.
        </div>
        Куала Лумпур (сыр, манго, лосось, икра Тобико)<br>
        Габу (сыр, огурец, перец, окунь, икра Тобико)<br>
        Спайси Тунец (тунец, огурец, авокадо, кунжут, соус Спайси)<br>
        Бонито Лосось (сыр, лосось, огурец, стружка тунца)<br>
        Роллы с креветкой, с жареным лососем, с икрой Тобико)<br>
        <img src="/test-discount/pic15.png" width="300" style="margin: 10px 0;"><br>

        <div style="color: #444444;font-family: PT_Sans bold;">
            Ролл "Аляска" (220 г) 11,90 руб. вместо 18 руб.
        </div>
        Сыр сливочный, креветка тигровая, огурец, икра Тобико<br>
        <img src="/test-discount/pic8.png" width="300" style="margin: 10px 0;"><br>

        <div style="color: #444444;font-family: PT_Sans bold;">
            Ролл "Микадо" (219 г) 11,90 руб. вместо 18,60 руб.
        </div>
        Креветка, угорь, огурец, соусы Унаги и ореховый, икра Тобико<br>
        <img src="/test-discount/pic9.png" width="300" style="margin: 10px 0;"><br>

        <div style="color: #444444;font-family: PT_Sans bold;">
            Ролл "Филадельфия Люкс" (275 г) 12,90 руб. вместо 21 руб.
        </div>
        Сыр сливочный, лосось свежий, икра лососевая<br>
        <img src="/test-discount/pic10.png" width="300" style="margin: 10px 0;"><br>

        <div style="color: #444444;font-family: PT_Sans bold;">
            Ролл "Тибет" (230 г) 10 руб. вместо 17,10 руб.
        </div>
        Сыр сливочный, угорь, огурец, авокадо, соус Унаги, кунжут<br>
        <img src="/test-discount/pic11.png" width="300" style="margin: 10px 0;"><br>

        <div style="color: #444444;font-family: PT_Sans bold;">
            Ролл "Хоккайдо" (240 г) 10,90 руб. вместо 17,90 руб.
        </div>
        Сыр сливочный, лосось свежий, угорь жареный, икра Тобико<br>
        <img src="/test-discount/pic12.png" width="300" style="margin: 10px 0;"><br>

        <div style="color: #444444;font-family: PT_Sans bold;">
            Ролл "Фудзияма" (250 г) 9 руб. вместо 15,30 руб.
        </div>
        Сыр сливочный, лосось свежий, огурец, салат, кунжут, соус Спайси<br>
        <img src="/test-discount/pic13.png" width="300" style="margin: 10px 0;"><br>

        <div style="color: #444444;font-family: PT_Sans bold;">
            Ролл "Эверест" (205 г) 8 руб. вместо 11,30 руб.
        </div>
        Сыр сливочный, лосось, авокадо, кунжут, соусы Унаги и Ореховый<br>
        <img src="/test-discount/pic14.png" width="300" style="margin: 10px 0;"><br>

        <div style="color: #444444;font-family: PT_Sans bold;">
            Гарниры
        </div>
        Стоимость:<br>
        - соус "Спайси" - 1,80 руб.<br>
        - имбирь (50 г) - 1,40 руб.<br>
        - соус "Кимчи" (45 г) - 2 руб.<br>
        - соус "Унаги" (45 г) - 1,50 руб.<br>
        - ореховый соус (45 г) - 2,80 руб.<br>
        - васаби с лимоном (15/2 г) - 0,60 руб.<br>
        - соевый соус слабосоленый (45 г) - 1 руб.<br>
        - соевый соус соленый (45 г) - 1,50 руб.<br>
        - салат Чука с ореховым соусом (75/30 г) - 4,80 руб.<br>
        - японский гарнир (соевый соус слабосоленый, имбирь, васаби с лимоном) - 3 руб.<br>
    </div>
</div>

<script>
    $(document).ready(function () {

        $(document).on('click','.js-gain-promo',function(){

            if (main.User.is_guest) {
                main.showErrorAut('Незарегистрированные пользователи не могут получить скидку');
                return false;
            }

            $.ajax({
                url: '/test/gain-promo',
                type: 'POST',
                dataType: "json",
                async:false,
                success:function (response) {

                    if(response.error){

                        $().toastmessage('showToast', {
                            text: response.message,
                            stayTime:5000,
                            type:'error'
                        });

                    }else {

                        (function($){

                            var modal = ModalWindow({
                                actionUrl : '/site/get-modal-window',
                                closeBtn : '.close-modal-btn',

                                renderBodyCallback: function ($form) {
                                    $form.find('.body-modal').html('<div class="message">'+response.message+'</div>')
                                }
                            });

                            modal.init();

                        })(jQuery)

                    }

                }
            });

        })

    })
</script>