<?php
$rows = $dataProvider->getModels();
?>

<?php foreach ($rows as $row):?>
    <tr>
        <td><?= Yii::$app->formatter->asDate(
                $row->date + Yii::$app->user->getTimezoneInSeconds(),
                'dd.MM.yyyy');?></td>
        <td class="<?= $row->changing < 0 ? 'money-minus' : 'money-plus'?>">
            <?= $row->changing < 0 ? $row->changing: '+' . $row->changing?> руб
        </td>
        <td><?= $row->message?></td>
    </tr>
<?php endforeach;?>

<?php if($settings['show-more-btn']):?>
    <?php if ($nextLink = $dataProvider->pagination->getLinks()['next'] ?? false): ?>
        <tr id="<?=$settings['replace-container-id']?>">
            <td colspan="3" style="padding: 0;">
                <div class="large-wide-button non-border btn-load-more" style="height: 50px;"
                     data-selector_replace="#<?=$settings['replace-container-id']?>"
                     data-href="<?=$nextLink?>&loadTime=<?=$settings['load-time']?>">
                    <p>Показать больше</p>
                </div>
            </td>
        </tr>
    <?php endif; ?>
<?php endif;?>