<?php

echo \yii\widgets\DetailView::widget([
    'model' => $model,
    'options' => ['class'=>'detail_view','style'=>'margin-top:30px'],
    'attributes' => [
        [
            'label' => 'Автор',
            'format' =>'raw',
            'value' => "<a data-pjax=false target=\"_blank\" href='/id{$model->user->id}'>".$model->user->name.' '.$model->user->surname."</a>" ,
            'captionOptions' => ['width'=>'70px','style'=>'height:50px'],
        ],
        [
            'label' => 'Ссылка на пост',
            'format' =>'raw',
            'value' => function($model){
                return "<a data-pjax=false target=\"_blank\" class='data-link' href='{$model->getLink()}'>{$model->post->data}</a>";
            },
            'captionOptions' => ['width'=>'100px'],
        ],
        [
            'label' => 'Оценка',
            'value' => $model->rating,
            'captionOptions' => ['width'=>'70px'],
            'contentOptions'=> ['style'=>'font-size:18px;line-height:22px']
        ],
        [
            'label' => 'Текст',
            'value' => $model->data,
            'captionOptions' => ['width'=>'70px'],
            'contentOptions'=> ['style'=>'padding:10px 30px;font-size:16px;line-height:22px;display:flex;width:700px;overflow:hidden;height: auto;flex-wrap: wrap;']
        ],
        [
            'label' => 'Фотографии',
            'format'=>'raw',
            'value' => function($model){
                if($model->count_photos){
                    $htmlStart = '<div class="photos-DW parent-container">';
                    $htmlBody = '';
                    $htmlEnd = '</div>';
                    foreach ($model->gallery as $value){
                        $htmlBody.="<img href='{$value->getPhotoPath()}' src='{$value->getPhotoPath()}'>";
                    }
                    return $htmlStart.$htmlBody.$htmlEnd;
                }
                return 'Нет фото';
            },
            'captionOptions' => ['width'=>'70px'],
            'contentOptions'=> ['style'=>'padding:10px 30px;']
        ],
        [
            'label' => 'Статус',
            'format' => 'raw',
            'value' => $model->getTextStatus(),
            'captionOptions' => ['width'=>'70px','style'=>'height:50px'],
            'contentOptions'=> ['style'=>'padding:10px 30px;']
        ],
        [
            'label' => 'Действие',
            'format' => 'raw',
            'value' => $model->getButtons(),
            'captionOptions' => ['width'=>'70px','style'=>'height:50px'],
            'contentOptions'=> ['style'=>'padding:10px 30px;']
        ],
    ],
]);

?>