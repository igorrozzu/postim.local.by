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
        if ($date >= $now) {
            return 'Сегодня в ' . $this->asDate($date, 'HH:mm');
        } elseif ($date >= $now - 3600 * 12 && $date < $now) {
            return 'Вчера в ' . $this->asDate($date, 'HH:mm');
        } else {
            return $this->asDate($date, 'dd MMMM в HH:mm');
        }
    }

    public function asCustomDuration($value)
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
            $interval = new DateInterval('P' . substr($value, 2));
            $isNegative = true;
        } else {
            $interval = new DateInterval($value);
            $isNegative = $interval->invert;
        }

        if ($isNegative) {
            return null;
        }

        if ($interval->y > 0) {
            return Yii::t('yii', '{delta, plural, =1{1 year} other{# years}}', ['delta' => $interval->y],
                $this->locale);
        }
        if ($interval->m > 0) {
            return Yii::t('yii', '{delta, plural, =1{1 month} other{# months}}', ['delta' => $interval->m],
                $this->locale);
        }
        if ($interval->d > 0) {
            return Yii::t('yii', '{delta, plural, =1{1 day} other{# days}}', ['delta' => $interval->d], $this->locale);
        }
        if ($interval->h > 0) {
            return Yii::t('yii', '{delta, plural, =1{1 hour} other{# hours}}', ['delta' => $interval->h],
                $this->locale);
        }
        if ($interval->i > 0) {
            return Yii::t('yii', '{delta, plural, =1{1 minute} other{# minutes}}', ['delta' => $interval->i],
                $this->locale);
        }

        return null;
    }

    public function asHostName(string $url)
    {
        $text = Helper::getDomainNameByUrl($url);
        $position = strpos($text, '/');

        if ($position !== false) {
            return substr($text, 0, $position);
        } else {
            return $text;
        }
    }

    /**
     * @param string $text
     * @param bool $en2ru
     * en2ru if set as true, otherwise ru2en
     * @return null|string|string[]
     */
    public function correctWrongKeyword(string $text, bool $en2ru = true)
    {
        $ruKeyword = [
            'й',
            'ц',
            'у',
            'к',
            'е',
            'н',
            'г',
            'ш',
            'щ',
            'з',
            'х',
            'ъ',
            'ф',
            'ы',
            'в',
            'а',
            'п',
            'р',
            'о',
            'л',
            'д',
            'ж',
            'э',
            'я',
            'ч',
            'с',
            'м',
            'и',
            'т',
            'ь',
            'б',
            'ю',
        ];
        $enKeyword = [
            'q',
            'w',
            'e',
            'r',
            't',
            'y',
            'u',
            'i',
            'o',
            'p',
            '[',
            ']',
            'a',
            's',
            'd',
            'f',
            'g',
            'h',
            'j',
            'k',
            'l',
            ';',
            '\'',
            'z',
            'x',
            'c',
            'v',
            'b',
            'n',
            'm',
            ',',
            '.',
        ];

        $result = $en2ru ? str_replace($enKeyword, $ruKeyword, $text) :
            str_replace($ruKeyword, $enKeyword, $text);

        return $result;
    }

    /**
     * Функция возвращает окончание для множественного числа слова на основании числа и массива окончаний
     * @param  $number int Число на основе которого нужно сформировать окончание
     * @param  $ending_arr  array Массив слов с правильными окончаниями для чисел (1, 2, 5),
     *         например array('комментарий', 'комментария', 'комментариев')
     * @return string
     */
    public function getNumEnding(int $number, array $ending_arr)
    {
        $number = $number % 100;
        if ($number >= 11 && $number <= 19) {
            $ending = $ending_arr[2];
        } else {
            $i = $number % 10;
            switch ($i) {
                case (1):
                    $ending = $ending_arr[0];
                    break;
                case (2):
                case (3):
                case (4):
                    $ending = $ending_arr[1];
                    break;
                default:
                    $ending = $ending_arr[2];
            }
        }
        return $ending;
    }
}