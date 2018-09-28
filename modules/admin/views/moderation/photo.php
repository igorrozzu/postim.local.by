<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Модерация фото';

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
    <h1 class="h1-c" style="margin-top: 35px">Модерация - Фото</h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => [
            'class' => 'table --custom-tbl-p',
        ],
        'layout' => "{items}\n{pager}",
        'rowOptions' => function ($data) {
            return ['style' => 'height:200px'];
        },
        'columns' => [
            [
                'attribute' => null,
                'format' => 'raw',
                'label' => 'Ссылка на пост',
                'headerOptions' => ['width' => '150px', 'class' => '--header-p'],
                'value' => function ($data) {
                    $alink = "<a data-pjax=false target=\"_blank\" class='data-link' href='{$data->getLink()}'>{$data->post->data}</a>";
                    return $alink;
                },

            ],
            [
                'attribute' => 'source',
                'format' => 'raw',
                'label' => 'Фото поста',
                'headerOptions' => ['width' => '400px', 'class' => '--header-p'],
                'value' => function ($data) {
                    return "<img href='{$data->getPhotoPath()}' class='image-link' style='height: 200px;width: auto;max-width: 400px; margin: 10px 0;' src='{$data->getPhotoPath()}'>";
                },

            ],

            [
                'attribute' => null,
                'format' => 'raw',
                'label' => 'Автор',
                'headerOptions' => ['width' => '100px', 'class' => '--header-p'],
                'value' => function ($data) {
                    return "{$data->user->name} {$data->user->surname}";
                },

            ],

            [
                'attribute' => 'status',
                'format' => 'raw',
                'label' => 'Статус',
                'headerOptions' => ['width' => '100px', 'class' => '--header-p'],
                'value' => function ($data) {
                    return $data->getTextStatus();
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
<script>
	$(document).ready(function () {
		$('.image-link').magnificPopup({type: 'image'});
	});
</script>
<?php

Pjax::end();

?>


