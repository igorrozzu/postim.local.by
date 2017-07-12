<?php
use yii\helpers\Html;

?>
<div class="password-reset">
    <p>Привет <?= Html::encode($user->name) ?>,</p>

    <p>Поздравляем с регистрацией. Ваш пароль для входа: </p>

    <p><?= $password ?></p>
</div>
