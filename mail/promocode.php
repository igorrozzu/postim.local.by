<?php

use app\components\Helper;
use yii\helpers\Json;
use yii\helpers\Url;

$hostName = Yii::$app->params['site.hostName'];
$discountUrl = "{$hostName}/{$discount->url_name}-d{$discount->id}";
$postUrl = "{$hostName}/{$discount->post->url_name}-p{$discount->post->id}";
$printOrderUrl = "{$hostName}/print-order?OID={$discountOrder->id}";

$timezone = $discountOrder->user->getTimezoneInSeconds();
?>

<tr style="background-color: #FFFFFF">
    <td>
        <span style="display: block; margin: 32px 20px 3px 20px;">
            Здравствуйте, <?= $discountOrder->user->name ?>.<br>
            Вы получили промокод на акцию.
        </span>

        <span style="display: block; margin: 15px 20px 20px 20px;">
            <a href="<?= $discountUrl?>" target="_blank" style="color: #3C5994; font-weight: bold; font-size: 18px;">
                <?= $discount->header?>
            </a>
        </span>

        <span style="display: inline-block; margin: 0 0 20px 20px; width: 280px;">
            <a href="<?= $discountUrl?>" target="_blank">
                <img src="<?= $hostName . $discount->getCover();?>"
                     style="float: left; display: block; width: 280px; max-height: 164px">
             </a>
        </span>
        <span style="display: inline-block; width: 260; margin: 0 0 20px 20px; vertical-align: top; font-size: 16px;">
            <span style="display: block; color: #6b778f; margin-bottom: 20px;">
                Промокод<span style="color: #444; font-weight: bold; margin-left: 5px;"><?= $discountOrder->promo_code?></span>
            </span>
            <span style="display: block; color: #6b778f; margin-bottom: 20px;">
                Срок действия
                <span style="color: #444; font-weight: bold; margin-left: 5px;">
                    <?= Yii::$app->formatter->asDate($discountOrder->date_finish + $timezone,
                        'до dd.MM.yyyy')?>
                </span>
            </span>
            <span style="display: block; color: #6b778f; margin-bottom: 15px;">
                Взят
                <span style="color: #444; font-weight: bold; margin-left: 5px;">
                    <?= Yii::$app->formatter->asDate($discountOrder->date_buy + $timezone,
                        'dd.MM.yyyy в HH:mm')?>
                </span>
            </span>
            <span style="display: block; font-size: 14px;">
                <a href="<?= $printOrderUrl;?>"
                   target="_blank" style="color: #3C5994; font-weight: bold;">Распечатать</a>
                 <span style="display: block;">
                     или сфотографируйте на телефон
                 </span>
            </span>
        </span>

        <span style="display: block; margin: 0 20px 20px 20px;">
            <?php foreach (Json::decode($discount->conditions) as $condition): ?>
                <span style="display: block;">
                - <?=$condition?>
                </span>
            <?php endforeach;?>

            <span style="display: block;">
                - Услуги (товары) предоставляются <?= $discount->post->requisites?>
            </span>

            <span style="display: block;">
                - Поставщик несет полную ответственность перед потребителем за достоверность информации
            </span>
        </span>

        <span style="display: block; margin: 0px 20px 0px 20px;">
            <a href="<?= $postUrl?>"
               target="_blank" style="color: #3C5994; font-weight: bold; font-size: 16px;">
                <?= $discount->post->data?>
            </a>
        </span>
        <span style="display: block; margin: 5px 20px 20px 20px; font-size: 16px;">
            <?php if ($discount->post['address']):?>
                <span style="display: block; color: #6b778f;">
                    Адрес:
                    <span style="color: #444; margin-left: 5px;">
                        <?= $discount->post->city['name'] . ', ' . $discount->post['address']?>
                    </span>
                </span>
            <?php endif;?>
            <?php if ($discount->post->info['phones']):?>
                <span style="display: block; color: #6b778f; margin-top: 3px;">
                    Телефон:
                    <span style="color: #444; margin-left: 5px;">
                        <?= implode(', ', $discount->post->info['phones'])?>
                    </span>
                </span>
            <?php endif;?>
            <?php if ($discount->post->info['web_site']):?>
                <span style="display: block; color: #6b778f;  margin-top: 3px;">
                    Веб-сайт:
                    <span style="color: #444; margin-left: 5px;">
                        <?= Yii::$app->formatter->asHostName($discount->post->info['web_site'])?>
                    </span>
                </span>
            <?php endif;?>
        </span>
        <span style="display: block; margin: 0px 20px 20px 20px; font-size: 12px;">
            <span style="color: #444; font-weight: bold;">
                Не забудьте поделится впечатлениями:
            </span>
            <a href="<?= $postUrl?>" target="_blank" style="color: #3C5994; font-weight: bold;">
                оставьте отзыв о <?= $discount->post->data?> на Postim.by
            </a>
        </span>

    </td>
</tr>