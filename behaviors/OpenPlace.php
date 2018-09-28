<?php

namespace app\behaviors;

use app\components\Helper;
use \yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use Yii;

class OpenPlace extends Behavior
{

    public $only_is_open = false;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'getOpenPlace',
        ];
    }

    public function getOpenPlace()
    {
        if ($this->owner->isRelationPopulated('workingHours')) {
            if ($this->owner->workingHours) {
                $currentDay = date('w') == 0 ? 7 : date('w');
                $workingHours = ArrayHelper::index(ArrayHelper::toArray($this->owner->workingHours), 'day_type');
                if ($this->isRoundTheClock($workingHours)) {
                    $this->owner->is_open = true;
                } else {
                    if (isset($workingHours[$currentDay])) {
                        $currentWorkingHours = $workingHours[$currentDay];

                        $this->owner->current_working_hours = [
                            'time_start' => $currentWorkingHours['time_start'],
                            'time_finish' => $currentWorkingHours['time_finish'],
                            'day_type' => $currentWorkingHours['day_type'],
                        ];
                        $currentTimestamp = Yii::$app->formatter->asTimestamp(Yii::$app->formatter->asTime(time() + Yii::$app->user->getTimezoneInSeconds(),
                            'short'));
                        $currentTime = idate('H', $currentTimestamp) * 3600 + idate('i',
                                $currentTimestamp) * 60 + idate('s', $currentTimestamp);
                        $currentTimePlus = $currentTime + 24 * 3600;

                        if (($currentTime >= $currentWorkingHours['time_start'] && $currentTime <= $currentWorkingHours['time_finish']) || ($currentTimePlus >= $currentWorkingHours['time_start'] && $currentTimePlus <= $currentWorkingHours['time_finish'])) {
                            $this->owner->is_open = true;

                            if (!$this->only_is_open) {
                                $nextDay = $currentDay == 7 ? 1 : $currentDay + 1;
                                if (isset($workingHours[$nextDay]) &&
                                    $workingHours[$nextDay]['time_start'] == $currentWorkingHours['time_finish']
                                ) {
                                    $this->owner->timeOpenOrClosed = '';
                                } else {
                                    $this->owner->timeOpenOrClosed = 'до ' . Yii::$app->formatter->asTime(($currentWorkingHours['time_finish']),
                                            'HH:mm');
                                }
                            }

                        } else {
                            if (!$this->only_is_open) {
                                if ($currentTime < $currentWorkingHours['time_start']) {
                                    $this->owner->timeOpenOrClosed = 'до ' . Yii::$app->formatter->asTime(($currentWorkingHours['time_start']),
                                            'HH:mm');
                                } else {
                                    if ($timeOpenOrClosed = $this->getNexWork($currentDay, $workingHours)) {
                                        $this->owner->timeOpenOrClosed = $timeOpenOrClosed;
                                    }
                                }
                            }

                            $this->owner->is_open = false;
                        }
                    } else {
                        if ($timeOpenOrClosed = $this->getNexWork($currentDay, $workingHours)) {
                            $this->owner->timeOpenOrClosed = $timeOpenOrClosed;
                        }
                    }
                }

            }

        }
    }

    private function isRoundTheClock($workingHours)
    {
        $foo = true;
        if (is_array($workingHours)) {
            foreach ($workingHours as $workingHour) {
                if ($workingHour['time_start'] !== 0 || $workingHour['time_finish'] !== 86400) {
                    $foo = false;
                }
            }
        } else {
            $foo = false;
        }
        return $foo;
    }

    private function getNexWork($currentDay, $workingHours)
    {

        $max = 7;
        $i = isset($workingHours[$currentDay + 1]) ? $currentDay + 1 : 1;
        while ($max) {
            if (isset($workingHours[$i]) && $workingHours[$i]) {
                return 'до ' . Yii::$app->formatter->asTime(($workingHours[$i]['time_start']),
                        'HH:mm') . ', ' . Helper::getShortNameDayById($i);
            }
            $i = $i == 7 ? 1 : $i + 1;
            $max--;
        }
    }
}