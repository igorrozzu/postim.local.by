<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

$currentUrl = yii\helpers\Url::current([], true);
$this->title = 'Модерация мест';



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
    <h1 class="h1-c" style="margin-top: 35px">Модерация мест</h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
            'class' => 'table --custom-tbl-p'
        ],
        'layout'=>"{items}\n{pager}",
        'columns' => [
            [
                'attribute' => null,
                'label' => 'Название',
                'format'=>'raw',
                'headerOptions' => ['width'=>'400px','height'=>'50px','style'=>'vertical-align: middle;','class' => '--header-p'],
                'value' => function($data){
                    if($data['main_id']){
                        $a = "<a data-pjax=false target=\"_blank\" class='data-link' href='/post/post-compare?id={$data['id']}&main_id={$data['main_id']}'>{$data['data']}</a>";
                        return $a;
                    }else{
                        $a = "<a data-pjax=false target=\"_blank\" class='data-link' href='/show-p{$data['id']}'>{$data['data']}</a>";
                        return $a;
                    }

                },
            ],

            [
                'attribute' => null,
                'format' => 'raw',
                'label' => 'Автор',
                'headerOptions' => ['width'=>'100px','height'=>'50px','style'=>'vertical-align: middle;','class' => '--header-p'],
                'value' => function($data){
                    return "<a data-pjax=false target=\"_blank\" class='data-link' href='/id{$data['user_id']}'>{$data['user_name']} {$data['surname']}</a>";
                },
            ],

            [
                'attribute' => null,
                'format' => 'raw',
                'label' => 'Тип',
                'headerOptions' => ['width'=>'50px','height'=>'50px','style'=>'vertical-align: middle;','class' => '--header-p'],
                'value' => function($data){
                    if($data['main_id']){
                        return 'Ред...';
                    }
                    return 'Новый';

                },
            ],

            [
                'attribute' => null,
                'format' => 'raw',
                'label' => 'Действие',
                'headerOptions' => ['width'=>'100px','height'=>'50px','style'=>'vertical-align: middle;','class' => '--header-p'],
                'value' => function($data){

                    $beginHtml = "<div class='data-grid-container-btn'>";
                    $bodyHtml = "";
                    $endHtml = "</div>";
                    if($data['main_id']){
                        $bodyHtml.="<a title='Одобрить' href='/admin/moderation/act-post?id={$data['id']}&main_id={$data['main_id']}&act=confirm' class='btn-moderation --confirm'></a>";
                        $bodyHtml.="<a style='margin-right: 30px' title='Одобрить и дать 10 опыта' href='/admin/moderation/act-post?id={$data['id']}&main_id={$data['main_id']}&act=confirm10' class='btn-moderation --confirm'><span class='prop-sb'>10</span></a>";
                        $bodyHtml.="<a title='Удалить' href='/admin/moderation/act-post?id={$data['id']}&main_id={$data['main_id']}&act=delete' class='btn-moderation --delete'></a>";
                        $bodyHtml.="<span data-id='{$data['id']}' data-main_id='{$data['main_id']}' title='Скрыть' class='btn-moderation --cancels'></span>";
                    }else{
                        $bodyHtml.="<a title='Одобрить' href='/admin/moderation/act-post?id={$data['id']}&act=confirm' class='btn-moderation --confirm'></a>";
                        $bodyHtml.="<a style='margin-right: 30px' title='Одобрить и дать 10 опыта' href='/admin/moderation/act-post?id={$data['id']}&act=confirm10' class='btn-moderation --confirm'><span class='prop-sb'>10</span></a>";
                        $bodyHtml.="<a title='Удалить' href='/admin/moderation/act-post?id={$data['id']}&act=delete' class='btn-moderation --delete'></a>";
                        $bodyHtml.="<span data-id='{$data['id']}' title='Скрыть' class='btn-moderation --cancels'></span>";
                    }


                    return $beginHtml.$bodyHtml.$endHtml;

                },
            ],
        ],
    ]);?>

</div>

<script>

    $(document).ready(function () {

        $(document).off('click','.btn-moderation.--cancels')
            .on('click','.btn-moderation.--cancels',function () {
                var id = $(this).data('id');
                var main_id = $(this).data('main_id');
                adminMain.initFormCancels(function (message) {

                    $.ajax({
                        url: '/admin/moderation/cancels-post',
                        type: "POST",
                        dataType: "json",
                        data: {
                            id: id,
                            main_id:main_id,
                            message: message
                        },
                        success: function (response) {
                            if (response.success){
                                $().toastmessage('showToast', {
                                    text: response.message,
                                    stayTime:5000,
                                    type: 'success'
                                });

                                $.pjax.reload({
                                    container: '#pjax-container-moderation',
                                    url: '<?=$currentUrl?>',
                                    push: false,
                                    replace: false
                                });

                                $('.container-blackout-popup-window').html('').hide();
                            } else {
                                $().toastmessage('showToast', {
                                    text: response.message,
                                    stayTime:8000,
                                    type: 'error'
                                });
                            }
                        }
                    });

                });
            });


    })

</script>

<?php
Pjax::end();
?>
