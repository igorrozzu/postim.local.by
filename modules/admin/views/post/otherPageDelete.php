<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

$currentUrl = yii\helpers\Url::current([], true);
$this->title = 'Удаление страниц';



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
    <h1 class="h1-c" style="margin-top: 35px">Удаление страниц</h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
            'class' => 'table --custom-tbl-p'
        ],
        'layout'=>"{items}\n{pager}",
        'columns' => [
            [
                'attribute' => 'url_name',
                'label' => 'Url',
                'headerOptions' => ['width'=>'200px','class' => '--header-p'],
            ],
            [
                'attribute' => 'h1',
                'label' => 'H1',
                'headerOptions' => ['width'=>'300px','class' => '--header-p'],
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


<?php
Pjax::end();
?>
