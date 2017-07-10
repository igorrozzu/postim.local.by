<?php
use \app\components\mainMenu\MainMenuWidget;
use \app\components\cardsPlaceWidget\CardsPlaceWidget;
?>
<div class="margin-top60"></div>
<div id="map_block" class="block-map"></div>

<div class="block-content">
    <h1 class="h1-c center-mx">Сервис поиска и добовления интересных мест,
        карта достопримечательностей Беларуси</h1>
    <?=MainMenuWidget::widget(['typeMenu'=>MainMenuWidget::$catalogMenu])?>
    <h2 class="h2-c">В центре внимания</h2>
    <div class="block-spotlight">
        <?=CardsPlaceWidget::widget()?>
    </div>
    <div class="clear-fix"></div>
    <h2 class="h2-c">Последние новости</h2>
    <div class="block-news">
        <div class="card-block ">
            <div class="card-photo" style="background-image: url('testP.png')">
                <div class="glass">
                    <div class="bookmarks-btn">12</div>
                </div>
            </div>
            <div class="card-block-info">
                <p class="info-head">Новости Минска</p>
                <p class="card-info">Открылся новый Собор на Малой Грузинской
                    в Минске. Для всех желающих 21.02.2017
                    вход бесплатный</p>
            </div>
            <div class="block-btn">
                <div class="comments">
                    <span class="icon"><img src="img/comments-icon.png"></span>
                    <span class="count-t">4</span>
                </div>
                <div class="views">
                    <span class="icon"><img src="img/views-icon.png"></span>
                    <span class="count-t">1474</span>
                </div>
            </div>
        </div>
        <div class="card-block ">
            <div class="card-photo" style="background-image: url('testP.png')">
                <div class="glass">
                    <div class="bookmarks-btn">12</div>
                </div>
            </div>
            <div class="card-block-info">
                <p class="info-head">Новости Минска</p>
                <p class="card-info">Открылся новый Собор на Малой Грузинской
                    в Минске. Для всех желающих 21.02.2017
                    вход бесплатный</p>
            </div>
            <div class="block-btn">
                <div class="comments">
                    <span class="icon"><img src="img/comments-icon.png"></span>
                    <span class="count-t">4</span>
                </div>
                <div class="views">
                    <span class="icon"><img src="img/views-icon.png"></span>
                    <span class="count-t">1474</span>
                </div>
            </div>
        </div>
        <div class="card-block ">
            <div class="card-photo" style="background-image: url('testP.png')">
                <div class="glass">
                    <div class="bookmarks-btn">12</div>
                </div>
            </div>
            <div class="card-block-info">
                <p class="info-head">Новости Минска</p>
                <p class="card-info">Открылся новый Собор на Малой Грузинской
                    в Минске. Для всех желающих 21.02.2017
                    вход бесплатный</p>
            </div>
            <div class="block-btn">
                <div class="comments">
                    <span class="icon"><img src="img/comments-icon.png"></span>
                    <span class="count-t">4</span>
                </div>
                <div class="views">
                    <span class="icon"><img src="img/views-icon.png"></span>
                    <span class="count-t">1474</span>
                </div>
            </div>
        </div>
        <div class="card-block N4">
            <div class="card-photo" style="background-image: url('testP.png')">
                <div class="glass">
                    <div class="bookmarks-btn">12</div>
                </div>
            </div>
            <div class="card-block-info">
                <p class="info-head">Новости Минска</p>
                <p class="card-info">Открылся новый Собор на Малой Грузинской
                    в Минске. Для всех желающих 21.02.2017
                    вход бесплатный</p>
            </div>
            <div class="block-btn">
                <div class="comments">
                    <span class="icon"><img src="img/comments-icon.png"></span>
                    <span class="count-t">4</span>
                </div>
                <div class="views">
                    <span class="icon"><img src="img/views-icon.png"></span>
                    <span class="count-t">1474</span>
                </div>
            </div>
        </div>
    </div>
    <div class="clear-fix"></div>
    <div class="btn-show-more">Показать больше новостей</div>
</div>
<div class="clear-fix"></div>
<div class="container-cities">
    <div class="block-cities">
        <h2 class="h2-v">Города Беларуси</h2>
        <div class="search-cities">
            <span class="btn-search2"></span>
            <input class="search-cities-i" type="text" placeholder="Поиск по названию, выберите ваш город">
        </div>
        <div class="autocomplete-result-search">
            <ul class="block-list-cities">
                <li>Минск</li>
                <li>Брест</li>
                <li>Витебск</li>
                <li>Гомель</li>
                <li>Гродно</li>
                <li>Могилев</li>
                <li>Минская область</li>
                <li>Брестская область</li>
                <li>Витебская область</li>
                <li>Гомельская область</li>
                <li>Гродненская область</li>
                <li>Могилевская область</li>
                <li>Беларусь</li>
                <li><b class="selected-letter">Б</b>арановичи</li>
                <li>Барань</li>
                <li>Белоозерск</li>
                <li>Белыничи</li>
                <li>Береза</li>
                <li>Березино</li>
                <li>Березовка</li>
                <li>Бешенковичи</li>
                <li>Бобруйск</li>
                <li>Большая Берестовица</li>
                <li>Борисов</li>
                <li>Брагин</li>
                <li>Браслав</li>
                <li>Буда-Кошелево</li>
                <li>Быхов</li>
                <li><b class="selected-letter">В</b>асилевичи</li>
                <li>Верхнедвинск</li>
                <li>Ветка</li>
                <li>Вилейка</li>
                <li>Волковыск</li>
                <li>Воложин</li>
                <li>Вороново</li>
                <li>Высокое</li>
                <li><b class="selected-letter">Г</b>анцевичи</li>
                <li>Глубокое</li>
                <li>Глуск</li>
                <li>Горки</li>
                <li>Городок</li>
                <li><b class="selected-letter">Д</b>авид-Городок</li>
                <li>Дзержинск</li>
                <li>Дисна</li>
                <li>Добруш</li>
                <li>Докшицы</li>
                <li>Дрибин</li>
                <li>Дрогичин</li>
                <li>Дубровно</li>
                <li>Дятлово</li>
                <li><b class="selected-letter">Е</b>льск</li>
                <li><b class="selected-letter">Ж</b>абинка</li>
                <li>Житковичи</li>
                <li>Жлобин</li>
                <li>Жодино</li>
                <li><b class="selected-letter">З</b>аславль</li>
                <li>Зельва</li>
                <li><b class="selected-letter">К</b>алинковичи</li>
                <li>Каменец</li>
                <li>Кировск</li>
                <li>Клецк</li>
                <li>Климовичи</li>
                <li>Кличев</li>
                <li>Кобрин</li>
                <li>Копыль</li>
                <li>Кореличи</li>
                <li>Корма</li>
                <li>Коссово</li>
                <li>Костюковичи</li>
                <li>Краснополье</li>
                <li>Кричев</li>
                <li>Круглое</li>
                <li>Крупки</li>
                <li><b class="selected-letter">Л</b>ельчицы</li>
                <li>Лепель</li>
                <li>Лида</li>
                <li>Лиозно</li>
                <li>Логойск</li>
                <li>Лоев</li>
                <li>Лунинец</li>
                <li>Любань</li>
                <li>Ляховичи</li>
                <li><b class="selected-letter">М</b>алорита</li>
                <li>Марьина Горка</li>
                <li>Микашевичи</li>
                <li>Миоры</li>
                <li>Мозырь</li>
                <li>Молодечно</li>
                <li>Мосты</li>
                <li>Мстиславль</li>
                <li>Мядель</li>
                <li><b class="selected-letter">Н</b>аровля</li>
                <li>Несвиж</li>
                <li>Новогрудок</li>
                <li>Новолукомль</li>
                <li>Новополоцк</li>
                <li><b class="selected-letter">О</b>ктябрьский</li>
                <li>Орша</li>
                <li>Осиповичи</li>
                <li>Островец</li>
                <li>Ошмяны</li>
                <li><b class="selected-letter">П</b>етриков</li>
                <li>Пинск</li>
                <li>Полоцк</li>
                <li>Поставы</li>
                <li>Пружаны</li>
                <li><b class="selected-letter">Р</b>ечица</li>
                <li>Рогачев</li>
                <li>Россоны</li>
                <li><b class="selected-letter">С</b>ветлогорск</li>
                <li>Свислочь</li>
                <li>Сенно</li>
                <li>Скидель</li>
                <li>Славгород</li>
                <li>Слоним</li>
                <li>Слуцк</li>
                <li>Смолевичи</li>
                <li>Сморгонь</li>
                <li>Солигорск</li>
                <li>Старые Дороги</li>
                <li>Столбцы</li>
                <li>Столин</li>
                <li><b class="selected-letter">Т</b>олочин</li>
                <li>Туров</li>
                <li><b class="selected-letter">У</b>зда</li>
                <li>Ушачи</li>
                <li><b class="selected-letter">Ф</b>аниполь</li>
                <li><b class="selected-letter">Х</b>ойники</li>
                <li>Хотимск</li>
                <li><b class="selected-letter">Ч</b>аусы</li>
                <li>Чашники</li>
                <li>Червень</li>
                <li>Чериков</li>
                <li>Чечерск</li>
                <li><b class="selected-letter">Ш</b>арковщина</li>
                <li>Шклов</li>
                <li>Шумилино</li>
                <li><b class="selected-letter">Щ</b>учин</li>
            </ul>
        </div>
    </div>
</div>