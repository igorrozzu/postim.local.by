<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use app\assets\AppAsset;
use app\assets\CustomScrollbarAsset;

AppAsset::register($this);
CustomScrollbarAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <?= Html::csrfMetaTags() ?>
    <title></title>
    <?php $this->head() ?>
    <!--яндекс карта-->
    <script src="https://api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU" type="text/javascript"> </script>
    <script type="text/javascript">
        var map;
        ymaps.ready(function(){
            map = new ymaps.Map("map_block", {
                center: [53.52, 28.20],
                zoom: 10
            });
        });
    </script>
    <!--яндекс карта-->

    <!-- адоптация для мобильный устройств-->
    <script>
        if (window.devicePixelRatio !== 1) { // Костыль для определения иных устройств, с коэффициентом отличным от 1
            var dpt = window.devicePixelRatio;
            var widthM = window.screen.width * dpt;
            var widthH = window.screen.height * dpt;
            document.write('<meta name="viewport" content="width=' + widthM+ ', height=' + widthH + '">');
        }
    </script>
    <!-- адоптация для мобильный устройств-->
</head>
<body>
<?php $this->beginBody() ?>
<!--левое меню-->
<div class="main-menu">
    <div class="header-menu">
        <div class="header-menu-title">Меню</div>
        <div class="left-arrow close-main-menu"></div>
    </div>
    <div class="menu-content">
        <ul class="menu-category">
            <li class="menu-category-list">
                <div class="category-list-title">
                    <a>Отдых</a>
                    <span class="open-list-btn"></span>
                </div>
                <ul class="menu-category-items">
                    <li class="menu-category-item"><a>Все места</a></li>
                    <li class="menu-category-item"><a>Отели</a></li>
                    <li class="menu-category-item"><a>Коттеджи и усадьбы</a></li>
                </ul>
            </li>
            <li class="menu-category-list">
                <div class="category-list-title">
                    <a>Где поесть</a>
                    <span class="open-list-btn"></span>
                </div>
                <ul class="menu-category-items">
                    <li class="menu-category-item"><a>Все места</a></li>
                    <li class="menu-category-item"><a>Отели</a></li>
                    <li class="menu-category-item"><a>Коттеджи и усадьбы</a></li>
                </ul>
            </li>
            <li class="menu-category-list">
                <div class="category-list-title">
                    <a>Где остановиться</a>
                    <span class="open-list-btn"></span>
                </div>
                <ul class="menu-category-items">
                    <li class="menu-category-item"><a>Все места</a></li>
                    <li class="menu-category-item"><a>Отели</a></li>
                    <li class="menu-category-item"><a>Коттеджи и усадьбы</a></li>
                </ul>
            </li>
            <li class="menu-category-list">
                <div class="category-list-title">
                    <a>Развлечения</a>
                    <span class="open-list-btn"></span>
                </div>
                <ul class="menu-category-items">
                    <li class="menu-category-item"><a>Все места</a></li>
                    <li class="menu-category-item"><a>Отели</a></li>
                    <li class="menu-category-item"><a>Коттеджи и усадьбы</a></li>
                    <li class="menu-category-item"><a>Все места</a></li>
                    <li class="menu-category-item"><a>Отели</a></li>
                    <li class="menu-category-item"><a>Коттеджи и усадьбы</a></li>
                    <li class="menu-category-item"><a>Базы отдыха</a></li>
                    <li class="menu-category-item"><a>Коттеджи и усадьбы</a></li>
                    <li class="menu-category-item"><a>Гостиницы</a></li>
                    <li class="menu-category-item"><a>Агроусадьбы</a></li>
                    <li class="menu-category-item"><a>Хостелы</a></li>
                </ul>
            </li>
            <li class="menu-category-list ">
                <div class="category-list-title">
                    <a>Интерсные места</a>
                    <span class="open-list-btn"></span>
                </div>
                <ul class="menu-category-items">
                    <li class="menu-category-item"><a>Все места</a></li>
                    <li class="menu-category-item"><a>Отели</a></li>
                    <li class="menu-category-item"><a>Коттеджи и усадьбы</a></li>
                    <li class="menu-category-item"><a>Базы отдыха</a></li>
                    <li class="menu-category-item"><a>Коттеджи и усадьбы</a></li>
                    <li class="menu-category-item"><a>Гостиницы</a></li>
                    <li class="menu-category-item"><a>Агроусадьбы</a></li>
                    <li class="menu-category-item"><a>Хостелы</a></li>
                </ul>
            </li>
            <li class="menu-category-list">
                <div class="news-list-title">
                    <a>Новости</a>
                </div>
            </li>
        </ul>
    </div>
</div>
<!--левое меню end-->
<!--верхнее меню-->
<div class="container-header">
    <div class="header">
        <div class="menu-btn"></div>
        <div class="logo"></div>
        <div class="select-city">Марьина Горка</div>
        <div class="btn_br">Добавить место</div>
        <div class="sign_in_btn">Войти</div>
        <div class="btn_add_place"></div>
        <div class="search_block">
            <input class="search" type="text" placeholder="Поиск">
            <span class="btn-search"></span>
        </div>
    </div>
</div>
<!--верхнее меню end-->

<div class="margin-top60"></div>
<div id="map_block" class="block-map"></div>
<?=$content;?>
<div class="block-widgets">
    <div class="widget"></div>
    <div class="widget"></div>
    <div class="widget"></div>
    <div class="widget"></div>
</div>
<div class="block-footer">
    <div class="block-footer-content">
        <div class="block-footer-btn">
            <ul class="menu-inline">
                <li><a>О сайте</a></li>
                <li><a>Правила</a></li>
                <li><a>Соглашение</a></li>
                <li><a>Реклама</a></li>
                <li><a>Обратная связь</a></li>
            </ul>
            <div class="block-social-icons">
                <div class="block-social">
                    <a class="social-btn-vk"></a>
                    <a class="social-btn-fb"></a>
                    <a class="social-btn-tw"></a>
                </div>
                <div class="block-social">
                    <a class="social-btn-ok"></a>
                    <a class="social-btn-inst"></a>
                    <a class="social-btn-viber"></a>
                </div>
            </div>
        </div>
        <div class="text-footer">
            ИП&nbsp;Борисов&nbsp;Владислав&nbsp;Александрович, УНП&nbsp;591251086. Режим&nbsp;работы&nbsp;–&nbsp;9:00&nbsp;до&nbsp;18:00. Тел:&nbsp;(029)&nbsp;718&nbsp;16&nbsp;66. Эл.&nbsp;почта:&nbsp;<span class="email-address">info@postim.by.</span> ©&nbsp;2016–2017&nbsp;Postim.by
        </div>
    </div>
</div>

<!--скрипт для меню-->
<script>

</script>
<!--скрипт для меню end-->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
