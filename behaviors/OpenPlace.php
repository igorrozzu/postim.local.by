<?php

namespace app\behaviors;

use app\components\Helper;
use \yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use Yii;

class OpenPlace extends Behavior{

    public $only_is_open=false;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'getOpenPlace'
        ];
    }

    public function getOpenPlace(){
        if($this->owner->isRelationPopulated('workingHours')){
            if($this->owner->workingHours){
                $currentDay = date('w')==0?7:date('w');
                $workingHours = ArrayHelper::index(ArrayHelper::toArray($this->owner->workingHours),'day_type');
                if(isset($workingHours[$currentDay])){
                    $currentWorkingHours = $workingHours[$currentDay];

                    $this->owner->current_working_hours= ['time_start' => $currentWorkingHours['time_start'],
                        'time_finish' => $currentWorkingHours['time_finish'],
                        'day_type'=>$currentWorkingHours['day_type']
                    ];
                    $currentTimestamp =  Yii::$app->formatter->asTimestamp(Yii::$app->formatter->asTime(time()+Yii::$app->user->getTimezoneInSeconds(), 'short'));
                    $currentTime = idate('H',$currentTimestamp)*3600+idate('i',$currentTimestamp)*60+idate('s',$currentTimestamp);

                    if($currentTime >= $currentWorkingHours['time_start'] && $currentTime <=$currentWorkingHours['time_finish']) {
                        $this->owner->is_open = true;

                        if(!$this->only_is_open){
                            $nextDay = $currentDay==7?1:$currentDay+1;
                            if(isset($workingHours[$nextDay]) &&
                                $workingHours[$nextDay]['time_start'] == $currentWorkingHours['time_finish']){
                                $this->owner->timeOpenOrClosed='';
                            }else{
                                $this->owner->timeOpenOrClosed='до '.Yii::$app->formatter->asTime(($currentWorkingHours['time_finish']), 'short');
                            }
                        }

                    } else {
                        if(!$this->only_is_open){
                            if($currentTime < $currentWorkingHours['time_start']){
                                $this->owner->timeOpenOrClosed='до '.Yii::$app->formatter->asTime(($currentWorkingHours['time_start']), 'short');
                            }else{
                                $max=7;
                                $i=isset($workingHours[$currentDay+1])?$currentDay+1:1;
                                while ($max){
                                    if(isset($workingHours[$i]) && $workingHours[$i]){
                                        if($workingHours[$i]['time_start']){
                                            $this->owner->timeOpenOrClosed='до '.Yii::$app->formatter->asTime(($currentWorkingHours['time_start']), 'short').', '.Helper::getShortNameDayById($i);
                                            break;
                                        }
                                    }
                                    $i = $i == 7 ? 1 : $i + 1;
                                    $max--;
                                }
                            }
                        }

                        $this->owner->is_open = false;
                    }
                }

            }

        }
    }
}