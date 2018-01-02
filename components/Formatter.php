<?php

namespace app\components;


use DateInterval;
use DateTime;
use Yii;

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

    public function asDuration($value, $implodeString = ', ', $negativeSign = '-')
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        if ($value instanceof DateInterval) {
            $isNegative = $value->invert;
            $interval = $value;
        } elseif (is_numeric($value)) {
            $isNegative = $value < 0;
            $zeroDateTime = (new DateTime())->setTimestamp(0);
            $valueDateTime = (new DateTime())->setTimestamp(abs($value));
            $interval = $valueDateTime->diff($zeroDateTime);
        } elseif (strpos($value, 'P-') === 0) {
            $interval = new DateInterval('P'.substr($value, 2));
            $isNegative = true;
        } else {
            $interval = new DateInterval($value);
            $isNegative = $interval->invert;
        }

        if ($interval->y > 0) {
            $parts[] = Yii::t('yii', '{delta, plural, =1{1 year} other{# years}}', ['delta' => $interval->y], $this->locale);
        }
        if ($interval->m > 0) {
            $parts[] = Yii::t('yii', '{delta, plural, =1{1 month} other{# months}}', ['delta' => $interval->m], $this->locale);
        }
        if ($interval->d > 0) {
            $parts[] = Yii::t('yii', '{delta, plural, =1{1 day} other{# days}}', ['delta' => $interval->d], $this->locale);
        }
        if ($interval->h > 0) {
            $parts[] = Yii::t('yii', '{delta, plural, =1{1 hour} other{# hours}}', ['delta' => $interval->h], $this->locale);
        }
        if ($interval->i > 0) {
            $parts[] = Yii::t('yii', '{delta, plural, =1{1 minute} other{# minutes}}', ['delta' => $interval->i], $this->locale);
        }

        if ($isNegative) {
            return null;
        }

        return empty($parts) ? $this->nullDisplay : (($isNegative ? $negativeSign : '') . implode($implodeString, $parts));
    }
}