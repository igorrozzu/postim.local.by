<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$currentUrl = yii\helpers\Url::current([], true);
?>
<div class="block-content">
    <h1 class="h1-c">Активные премиум бизнес-аккаунты</h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
            'class' => 'table --custom-tbl-p',
        ],
        'layout' => "{items}\n{pager}",
        'columns' => [
            [
                'attribute' => 'user_id',
                'format' => 'text',
                'headerOptions' => ['width' => '70px', 'class' => '--header-p'],

            ],
            [
                'attribute' => 'post_id',
                'format' => 'text',
                'headerOptions' => ['width' => '70px', 'class' => '--header-p'],
            ],
            [
                'attribute' => null,
                'format' => 'text',
                'label' => 'Имя и фамилия',
                'headerOptions' => ['class' => '--header-p'],
                'value' => function ($data) {
                    return $data->full_name;
                },
            ],
            [
                'attribute' => null,
                'format' => 'text',
                'label' => 'Email',
                'headerOptions' => ['class' => '--header-p'],
                'value' => function ($data) {
                    return $data->user->email;
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
                'attribute' => 'premium_finish_date',
                'format' => 'html',
                'headerOptions' => ['class' => '--header-p'],
                'value' => function ($data) {
                    $className = $data->premium_finish_date <= time() ? 'close-txt' : 'open-txt';

                    return '<span class="' . $className . '">' . Yii::$app->formatter->asDate(
                            $data->premium_finish_date + Yii::$app->user->getTimezoneInSeconds(),
                            'dd.MM.yyyy, HH:mm:ss') . '</span>';
                },
            ],
            [
                'attribute' => null,
                'format' => 'raw',
                'label' => 'Права',
                'headerOptions' => ['width' => '60px', 'class' => '--header-p'],
                'value' => function ($data, $key) use ($currentUrl) {
                    return Html::activeDropDownList(
                        $data,
                        'status',
                        ['confirm' => 'Да', 'remove' => 'Нет'],
                        [
                            'id' => $key['user_id'] . '-' . $key['post_id'],
                            'onchange' => "
                                   $.ajax({
                                     url: '/admin/biz/change-status-business',
                                     type: 'post',
                                     data: { 
                                         'user_id':{$key['user_id']},
                                         'post_id':{$key['post_id']},
                                         'action':$(\"#{$key['user_id']}-{$key['post_id']}\").val() 
                                     },
                                     success: function(response) {
                                            
                                            if(response.success){
                                                if(response.action == 'remove'){
                                                    $.pjax.reload({
                                                        container: '#pjax-container-add-biz',
                                                        url: '{$currentUrl}',
                                                        push: false,
                                                        replace: false
                                                    });
                                                }
                                            }
                                     
                                        }
                                    });
                                ",
                        ]
                    );
                },
            ],
        ],
    ]); ?>

</div>