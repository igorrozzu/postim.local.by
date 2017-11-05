<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

$currentUrl = yii\helpers\Url::current([], true);
$this->title = 'Модерация жалоб';



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
    <h1 class="h1-c" style="margin-top: 35px">Модерация-Жалобы</h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
            'class' => 'table --custom-tbl-p'
        ],
        'layout'=>"{items}\n{pager}",
        'columns' => [
            [
                'attribute' => 'data',
                'format' => 'raw',
                'label' => 'Название',
                'headerOptions' => ['width'=>'550px','class' => '--header-p'],
                'value'=>function($data){
                    return $data->getInfoForName();
                }

            ],
            [
                'attribute' => 'user_id',
                'format' => 'raw',
                'label' => 'Пользователь',
                'headerOptions' => ['width'=>'100px','class' => '--header-p'],
                'value' => function($data){
                    return "<a data-pjax=false target=\"_blank\" class='data-link' href='/id{$data->user->id}'>{$data->user->name} {$data->user->surname}</a>" ;
                }
            ],

            [
                'attribute' => 'status',
                'format' => 'raw',
                'label' => 'Статус',
                'headerOptions' => ['class' => '--header-p'],
                'value' => function($data){
                    return $data->getStatus();
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

<script>
    $(document).ready(function () {
        $(document).off('click','.btn-moderation.--cancels')
            .on('click','.btn-moderation.--cancels',function () {
                var $btn_confirm = $(this).parents('.data-grid-container-btn').find('.--confirm');
                adminMain.initFormCancels($(this).data('id'),function () {
                    $btn_confirm.trigger('click');
                });
            });
    })
</script>

<?php
Pjax::end();
?>



