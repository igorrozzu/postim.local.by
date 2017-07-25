<?php

namespace app\components;


use DateTime;

class Formatter extends \yii\i18n\Formatter
{
    public function printDate($date)
    {
        $now = mktime(0, 0, 0);
        if($date >= $now) {
            return 'Сегодня в ' . $this->asDate($date, 'HH:mm');
        } elseif($date >= $now - 3600 * 12 && $date < $now) {
            return 'Вчера в ' . $this->asDate($date, 'HH:mm');
        } else {
            return $this->asDate($date, 'dd MMMM в HH:mm');
        }
    }
}