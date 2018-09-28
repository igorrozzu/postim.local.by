<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

$currentUrl = yii\helpers\Url::current([], true);
$this->title = 'Особенности';


Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => false,
    'id' => 'pjax-container-moderation',
    'linkSelector' => '#pjax-container-moderation a',
    'formSelector' => '#pjax-container-moderation form',
]);

?>

<div class="margin-top60"></div>
<div class="block-content">
    <h1 class="h1-c" style="margin-top: 35px">Особенности</h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
            'class' => 'table --custom-tbl-p',
        ],
        'layout' => "{items}\n{pager}",
        'columns' => [
            [
                'attribute' => 'name',
                'label' => 'Название',
                'headerOptions' => ['width' => '250px', 'class' => '--header-p'],
            ],
            [
                'attribute' => 'type',
                'label' => 'Тип',
                'headerOptions' => ['width' => '100px', 'class' => '--header-p'],
                'value' => function ($data) {
                    return $data->getLabelType();
                },
            ],
            [
                'attribute' => 'main_features',
                'label' => 'Зависит от ...',
                'headerOptions' => ['width' => '250px', 'class' => '--header-p'],
                'value' => function ($data) {
                    return $data->getNameMainFeatures(1);
                },
            ],
            [
                'attribute' => 'filter_status',
                'label' => 'Статус фильтра',
                'format' => 'raw',
                'headerOptions' => ['width' => '100px', 'class' => '--header-p'],
                'value' => function ($data) {
                    return "<a class='data-link' href='/admin/features/change?status=" . (abs($data->filter_status - 1)) . "&id={$data->id}'>{$data->getLabelFilterStatus()}</a>";
                },
            ],

            [
                'attribute' => null,
                'format' => 'raw',
                'label' => 'Действие',
                'headerOptions' => ['width' => '100px', 'class' => '--header-p'],
                'value' => function ($data) {
                    return $data->getButtons();
                },
            ],
        ],
    ]); ?>


</div>


<?php
Pjax::end();
?>
