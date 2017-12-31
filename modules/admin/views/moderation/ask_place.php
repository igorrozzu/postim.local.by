<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

$currentUrl = yii\helpers\Url::current([], true);
$this->title = 'Вопросы и ответы';



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
    <h1 class="h1-c" style="margin-top: 35px">Вопросы и ответы</h1>

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
                'label' => 'Текст',
                'headerOptions' => ['width'=>'550px','class' => '--header-p'],
                'value'=>function($data){
                    return "<a data-pjax=false target=\"_blank\" class='data-link' href='{$data->getHrefToPost()}'>{$data->data}</a>";
                }

            ],

            [
                'attribute' => 'user_id',
                'format' => 'raw',
                'label' => 'Пользователь',
                'headerOptions' => ['width'=>'30px','class' => '--header-p'],
                'value' => function($data){
                    return "<a data-pjax=false target=\"_blank\" class='data-link' href='/id{$data->user->id}'>{$data->user->name} {$data->user->surname}</a>";
                }
            ],

            [
                'attribute' => null,
                'format' => 'raw',
                'label' => 'Есть ответ?',
                'headerOptions' => ['width'=>'100px','class' => '--header-p'],
                'value' => function($data){
                    if($data->hasAnswer()){
                        return 'Да';
                    }
                    return 'Нет';
                }
            ],


        ],
    ]);?>
</div>


<?php
Pjax::end();
?>



