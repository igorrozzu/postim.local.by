<?php
use app\components\cardsPromoWidget\CardsPromoWidget;
use yii\helpers\Url;
use yii\widgets\Pjax;
?>
<div class="margin-top60"></div>
<div class="block-content">
    <div class="bread-crumb">
        <a class="pre" href="#">Главная</a>
        <span class="separator"></span>
        <p>Мои сертификаты</p>
    </div>
    <h1 class="h1-v">Мои сертификаты</h1>
</div>
<?php
Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => false,
    'id' => 'feeds-of-user',
    'linkSelector' => '.feeds-btn-bar a',
    'formSelector' => false,
])
?>
<div class="block-flex-white">
    <div class="block-content">
        <div class="menu-btns-card feeds-btn-bar">
            <a href="<?=Url::to(['user/sertifikaty'])?>">
                <div class="btn2-menu <?=($status === 'active') ? 'active' : ''?>">
                    Действующие
                </div>
            </a>
            <a href="<?=Url::to(['user/sertifikaty', 'status' => 'unactive', 'type' => 'certificate'])?>">
                <div class="btn2-menu <?=($status === 'unactive') ? 'active' : ''?>">
                    Использованые
                </div>
            </a>
            <a href="<?=Url::to(['user/sertifikaty', 'status' => 'all', 'type' => 'certificate'])?>" >
                <div class="btn2-menu <?=($status === 'all') ? 'active' : ''?>">
                    Все
                </div>
            </a>
        </div>
    </div>
</div>

<div class="block-content">
    <div class="container-cards-promo">
    <?= CardsPromoWidget::widget([
        'dataProvider' => $dataProvider,
        'settings' => [
            'show-more-btn' => true,
            'replace-container-id' => 'feed-certificate',
            'load-time' => $loadTime,
            'show-more-btn-text' => 'Показать больше сертификатов',
            'not-found-text' => 'Вы пока не купили ни одного сертификата.',
        ]
    ]);?>
    </div>
</div>
<?php
Pjax::end();
?>
<div style="margin-bottom:30px;"></div>
