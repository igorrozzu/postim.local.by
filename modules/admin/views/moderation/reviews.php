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
        'layout' => '{items}{pager}',
    ]);

    ?>


</div>
<script>
	$(document).ready(function () {

		$(document).off('click', '.btn-moderation.--cancels')
				.on('click', '.btn-moderation.--cancels', function () {
					var id = $(this).data('id');
					adminMain.initFormCancels(function (message) {

						$.ajax({
							url: '/admin/moderation/cancels-reviews',
							type: "POST",
							dataType: "json",
							data: {
								id: id,
								message: message
							},
							success: function (response) {
								if (response.success) {
									$().toastmessage('showToast', {
										text: response.message,
										stayTime: 5000,
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
										stayTime: 8000,
										type: 'error'
									});
								}
							}
						});

					});
				});

		$('.parent-container').magnificPopup({
			delegate: 'img', // child items selector, by clicking on it popup will open
			type: 'image',
			gallery: {enabled: true}
		});
	});
</script>
<?php
Pjax::end();
?>


