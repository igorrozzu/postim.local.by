<?php
namespace app\components;

class User extends \yii\web\User{

    public static $DEFAULT_TIME_ZONE=3;

    public function getTimezoneInSeconds(){
        if($this->isGuest){
            return self::$DEFAULT_TIME_ZONE * 3600;
        }else{
            return $this->identity->timezone_offset_in_hour*3600;
        }
    }

}