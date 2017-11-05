<?php

use yii\widgets\Pjax;
use yii\widgets\ListView;

$this->title = 'Модерация отзывов';
$currentUrl = yii\helpers\Url::current([], true);

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
    <h1 class="h1-c" style="margin-top: 35px">Модерация - Отзывов</h1>

    <?php

    echo ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '__reviews',
        'layout' => '{items}{pager}'
    ]);

    ?>


</div>
<script>
    $(document).ready(function () {

        $(document).off('click','.btn-moderation.--cancels')
            .on('click','.btn-moderation.--cancels',function () {
                adminMain.initFormCancels($(this).data('id'),function () {
                    $.pjax.reload({
                        container: '#pjax-container-moderation',
                        url: '<?=$currentUrl?>',
                        push: false,
                        replace: false
                    });
                });
            });

        $('.parent-container').magnificPopup({
            delegate: 'img', // child items selector, by clicking on it popup will open
            type: 'image',
            gallery:{enabled:true}
        });
    });
</script>
<?php
Pjax::end();
?>


