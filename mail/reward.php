<?php
use yii\helpers\Html;


?>

<tr style="background-color: #FFFFFF">
    <td>
        <span style="display: block; margin: 32px 20px 3px 20px;">
            Здравствуйте, <?= $user->name?>.
        </span>
        <span style="display: block; margin: 0px 20px 20px 20px;">
            <?=$message?>
        </span>
    </td>
</tr>