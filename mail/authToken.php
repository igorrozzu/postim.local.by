<?php
use yii\helpers\Html;

$confirmLink = Yii::$app->urlManager->createAbsoluteUrl([
    'site/confirm-account',
    'token' => $user->auth_token
]);
?>

<p>Привет <?= Html::encode($user->name) ?>,</p>
<p>Пройдите по ссылке для подтверждения аккаунта:</p>

<p><?= Html::a('Подтвердить', $confirmLink) ?></p>