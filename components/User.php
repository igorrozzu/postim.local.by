<?php
namespace app\components;

use Yii;

class User extends \yii\web\User{

    public static $DEFAULT_TIME_ZONE=3;

    public function getTimezoneInSeconds(){
        if($this->isGuest){
            return self::$DEFAULT_TIME_ZONE * 3600;
        }else{
            return $this->identity->timezone_offset_in_hour*3600;
        }
    }

    public function getPhoto(){

		if($this->isGuest){
			return '/img/default-profile-icon.png';
		}else{
			$userPhotoDir = '/user_photo/' . $this->identity->id;
			if (is_dir(Yii::getAlias('@webroot' . $userPhotoDir))) {
				return $userPhotoDir . '/' . Yii::$app->params['user.photoName'] . '?' . $this->identity->photo_hash;
			} else {
				return '/img/default-profile-icon.png';
			}
		}

	}

	public function getName(){
		if($this->isGuest){
			return 'Гость';
		}else{
			return $this->identity->name.' '.$this->identity->surname;
		}
	}

	public function isModerator(){
        if($this->isGuest){
            return false;
        }else{
            return $this->identity->role >= 2 ?true :false;
        }
    }

}