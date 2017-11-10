<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

$currentUrl = yii\helpers\Url::current([], true);
$this->title = 'Удаление особенностей из категории';



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
    <h1 class="h1-c" style="margin-top: 35px">Удаление особенностей из категории</h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
            'class' => 'table --custom-tbl-p'
        ],
        'layout'=>"{items}\n{pager}",
        'columns' => [
            [
                'attribute' => 'features_id',
                'label' => 'Особенность',
                'headerOptions' => ['width'=>'250px','class' => '--header-p'],
                'value' => function($data){
                    return $data->features->name;
                }
            ],
            [
                'attribute' => 'category_id',
                'label' => 'Категории',
                'headerOptions' => ['width'=>'100px','class' => '--header-p'],
                'value' => function($data){
                    return $data->category->name;
                }
            ],

            [
                'attribute' => null,
                'format' => 'raw',
                'label' => 'Действие',
                'headerOptions' => ['width'=>'100px','class' => '--header-p'],
                'value' => function($data){
                    return $data->getButtons();
                },
            ],
        ],
    ]);?>







    <h1 class="h1-c" style="margin-top: 35px">Удаление особенностей из подкатегории</h1>

    <?= GridView::widget([
        'dataProvider' => $dataProviderUnder,
        'filterModel' => $searchModelUnder,
        'tableOptions' => [
            'class' => 'table --custom-tbl-p'
        ],
        'layout'=>"{items}\n{pager}",
        'columns' => [
            [
                'attribute' => 'features_id',
                'label' => 'Особенность',
                'headerOptions' => ['width'=>'250px','class' => '--header-p'],
                'value' => function($data){
                    return $data->features->name;
                }
            ],
            [
                'attribute' => 'under_category_id',
                'label' => 'подкатегории',
                'headerOptions' => ['width'=>'100px','class' => '--header-p'],
                'value' => function($data){
                    return $data->underCategory->name;
                }
            ],

            [
                'attribute' => null,
                'format' => 'raw',
                'label' => 'Действие',
                'headerOptions' => ['width'=>'100px','class' => '--header-p'],
                'value' => function($data){
                    return $data->getButtons();
                },
            ],
        ],
    ]);?>


</div>


<?php
Pjax::end();
?>
