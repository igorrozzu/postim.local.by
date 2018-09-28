<?php

$this->title = 'Бизнес-аккаунты';

use yii\widgets\Pjax;

Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => false,
    'id' => 'pjax-container-add-biz',
    'linkSelector' => '#pjax-container-add-biz a',
    'formSelector' => '#pjax-container-add-biz form',
]);
?>

<div class="margin-top60"></div>

<?php

echo $this->render('bid_order_table', [
    'dataProvider' => $dataProvider,
    'searchModel' => $searchModel,
]);

?>

<?php

Pjax::end();
?>
