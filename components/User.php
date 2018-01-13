<?php
namespace app\components;

use app\models\entities\OwnerPost;
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

    public function isOwnerPost()
    {
        if ($this->isGuest) {
            return false;
        }

        $isUserOwner = OwnerPost::find()
            ->where(['owner_id' => $this->getId()])
            ->one();
        if (!isset($isUserOwner)) {
            return false;
        }

        return true;
    }

    public function isOwnerThisPost(int $postId): bool
    {
        if ($this->isGuest) {
            return false;
        }

        $isCurrentUserOwner = OwnerPost::find()
            ->where([
                OwnerPost::tableName() . '.owner_id' => $this->getId(),
                OwnerPost::tableName() . '.post_id' => $postId,
            ])->one();

        return isset($isCurrentUserOwner);
    }

    public function generationSecretToken(){

	    if($this->isModerator()){
            $date =Yii::$app->formatter->asDate(time(),'dd:MM:yyy');
            $key = 'ueqrwtSDCFVHBJNKrupiopo,nmcxYTUGHJNMhnuhumjmii,kool,.79846523,.mnbv[OP;ILJKHftyrefgrertfgb';

            $token = mb_substr(md5($this->getId().$date),0,100). mb_substr(md5($key),0,100);

            return $token;
        }

        return false;

    }

}