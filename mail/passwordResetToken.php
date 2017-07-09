<?php
use yii\helpers\Html;


$resetLink = Yii::$app->urlManager->createAbsoluteUrl([
    'site/reset-password',
    'token' => $user->password_reset_token
]);

?>
<div class="password-reset">
    <p>Привет <?= Html::encode($user->name) ?>,</p>

    <p>Нажми на кнопку, чтобы восстановить пароль</p>

    <p><?= Html::a('Восстановить', $resetLink) ?></p>
</div>
