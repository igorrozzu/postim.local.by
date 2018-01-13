<?php

use app\models\Discounts;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

$currentUrl = yii\helpers\Url::current([], true);
$this->title = 'Модерация скидок';



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
    <h1 class="h1-c" style="margin-top: 35px">Модерация скидок</h1>

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
                'value' => function($discount) {
                    $url = Url::to(['/discount/read', 'url' => $discount->url_name,
                        'discountId' => $discount->id]);
                    $a = "<a data-pjax=false target=\"_blank\" class='data-link' 
                            href='{$url}'>{$discount->header}</a>";
                    return $a;

                },
            ],

            [
                'attribute' => null,
                'format' => 'raw',
                'label' => 'Добавил',
                'headerOptions' => ['width'=>'100px','height'=>'50px','style'=>'vertical-align: middle;','class' => '--header-p'],
                'value' => function($discount) {
                    return "<a data-pjax=false target=\"_blank\" class='data-link' 
                    href='/id{$discount->user->id}'>{$discount->user->getFullName()}</a>";
                },
            ],

            [
                'attribute' => null,
                'format' => 'raw',
                'label' => 'Тип',
                'headerOptions' => ['width'=>'50px','height'=>'50px','style'=>'vertical-align: middle;','class' => '--header-p'],
                'value' => function($discount) {
                    switch ($discount->status) {
                        case Discounts::STATUS['editing']: return 'Ред...';
                        case Discounts::STATUS['moderation']: return 'Новый';
                        default: return 'Неверный тип';
                    }
                },
            ],

            [
                'attribute' => null,
                'format' => 'raw',
                'label' => 'Действие',
                'headerOptions' => ['width'=>'100px','height'=>'50px','style'=>'vertical-align: middle;','class' => '--header-p'],
                'value' => function($discount) {

                    $beginHtml = "<div class='data-grid-container-btn'>";
                    $bodyHtml = "";
                    $endHtml = "</div>";

                    $bodyHtml.="<a href='". Url::to(['discount/edit',
                            'id' => $discount->id]) ."' style='margin-right: 30px' title='Редактировать'>Ред.</a>";
                    $bodyHtml.="<a href='". Url::to(['discount/confirm',
                            'id' => $discount->id]) ."' title='Одобрить' class='btn-moderation --confirm'></a>";
                    $bodyHtml.="<span data-id='{$discount->id}' title='Скрыть' class='btn-moderation --cancels'></span>";

                    return $beginHtml.$bodyHtml.$endHtml;
                },
            ],
        ],
    ]);?>

</div>

<script>
$(document).ready(function () {
    <?php if(Yii::$app->session->hasFlash('error')):?>
        $().toastmessage('showToast', {
            text: '<?=Yii::$app->session->getFlash('error')?>',
            stayTime: 5000,
            type: 'error'
        });
    <?php endif;?>
    <?php if(Yii::$app->session->hasFlash('success')):?>
        $().toastmessage('showToast', {
            text: '<?=Yii::$app->session->getFlash('success')?>',
            stayTime: 5000,
            type: 'success'
        });
    <?php endif;?>

    $(document).off('click','.btn-moderation.--cancels')
        .on('click','.btn-moderation.--cancels',function () {
            var id = $(this).data('id');
            adminMain.initFormCancels(function (message) {

                $.ajax({
                    url: '/admin/discount/hide',
                    type: "POST",
                    dataType: "json",
                    data: {
                        id: id,
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
