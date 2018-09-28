<?php

/* @var $this \yii\web\View */

/* @var $content string */

use yii\helpers\Html;

$hostName = Yii::$app->params['site.hostName'];
?>
<?php $this->beginPage() ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html lang="<?= Yii::$app->language ?>" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <?= Html::csrfMetaTags() ?>
    <title></title>
    <?php $this->head() ?>

</head>
<body style="-webkit-print-color-adjust: exact !important;color-adjust: exact !important;
margin: 0; padding: 0; background-color: #f1f2f4;">
<?php $this->beginBody() ?>
<table border="0" cellpadding="0" cellspacing="0" style="margin: 0 auto; padding-top: 20px; max-width: 600px;
        font-family: Tahoma, Verdana, Helvetica, sans-serif; color: #444444; font-size: 15px;">
    <tr>
        <td style="background-color: #3c5994; height: 60px;">
            <img style="display:block; margin-left: 20px; color: #FFFFFF;margin-top: 10px" alt="Postim.by" border="0"
                 src="<?= $hostName ?>/img/logo.png">
        </td>
    </tr>

    <?= $content; ?>
</table>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
