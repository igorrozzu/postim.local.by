<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$currentUrl = yii\helpers\Url::current([], true);
?>
<div class="block-content">
    <h1 class="h1-c">Заявки на бизнес-аккаунты</h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
            'class' => 'table --custom-tbl-p',
            'style' => 'width:100%;',
        ],
        'layout'=>"{items}\n{pager}",
        'columns' => [

            [
                'attribute' => null,
                'format' => 'text',
                'label' => 'Имя и фамилия',
                'headerOptions' => ['class' => '--header-p'],
                'value' => function($data){
                    return $data->full_name;
                },
            ],
            [
                'attribute' => null,
                'format' => 'text',
                'label' => 'Название компании',
                'headerOptions' => ['class' => '--header-p'],
                'value' => function($data){
                    return $data->company_name;
                },
            ],
            [
                'attribute' => null,
                'format' => 'text',
                'label' => 'Email',
                'headerOptions' => ['class' => '--header-p'],
                'value' => function($data){
                    return $data->email;
                },
            ],
            [
                'attribute' => 'position',
                'format' => 'text',
                'headerOptions' => ['class' => '--header-p'],
            ],
            [
                'attribute' => 'phone',
                'format' => 'text',
                'headerOptions' => ['class' => '--header-p'],
            ],

            [
                'attribute' => null,
                'format' => 'raw',
                'label' => 'Статус',
                'headerOptions' => ['class' => '--header-p'],
                'value' => function($data){
                    return $data->getStatusText($data->status);
                },
            ],

            [
                'attribute' => null,
                'format' => 'raw',
                'label' => 'Тип',
                'headerOptions' => ['class' => '--header-p'],
                'value' => function($data){
                    return $data->getStatusText($data->type);
                },
            ],

            [
                'attribute' => null,
                'format' => 'raw',
                'label' => 'Действие',
                'headerOptions' => ['class' => '--header-p'],
                'value' => function($data){
                    return $data->getButtons();
                },
            ],
        ],
    ]);?>

</div>