<?php
use yii\helpers\Html;

?>

<tr style="background-color: #FFFFFF">
    <td>
        <span style="display: block; margin: 20px 20px 0px 20px;">
            Здравствуйте, <?= $userName?>.<br>
            Вы брали промокод на акцию: <b><?= $discountTitle?>.</b>
        </span>
        <span style="display: block; margin: 20px;">
            <b>Если Вы воспользовались предложением, поделитесь своими впечатлениями с другими:</b>
            <a href="<?= $postUrl?>" target="_blank" style="color: #3C5994;">
                оставьте отзыв о <?= $postTitle?> на Postim.by.
            </a>
        </span>
        <a href="<?= $postUrl?>" style="text-decoration: none; display: block;margin:0px 0px 2px 20px; height: 42px;
width: 270px;" target="_blank">
            <span style="background-color: #CF4D43;
-webkit-text-size-adjust:none;
display: block;
border-bottom: 2px solid #a82828;
border-radius: 3px;
-webkit-border-radius: 3px;
-moz-border-radius: 3px;
height: 42px;
width: 270px;
color: #ffffff;
text-align: center;
line-height: 42px;
cursor: pointer;">Добавить отзыв</span>
        </a>
        <span style="display: block; margin: 20px;">
            Благодарим Вас за комментарий. Удачи!
        </span>
    </td>
</tr>