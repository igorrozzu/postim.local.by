<?php

namespace app\models;

use app\components\ImageHelper;
use app\components\MailSender;
use app\components\social\attributes\SocialAuthAttr;
use app\components\social\attributes\SocialAuthAttributes;
use Yii;
use yii\base\Model;


class SocialRegister extends Model
{
    public $name;
    public $email;

    const MAX_NAME_LENGTH = 25;
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name','email'], 'filter', 'filter' => 'trim'],
            [['name','email'], 'required', 'message'=> 'Поле обязательно для заполнения'],
            ['name', 'string', 'max' => self::MAX_NAME_LENGTH, 'tooLong'=> 'Не более {max} символов'],
            ['email', 'email', 'message' => 'Некорректный email-адрес'],
            ['email', 'unique', 'targetClass' => '\app\models\User',
                'message' => 'Пользователь с таким email-адресом уже существует'],
        ];
    }

    public function createUser(SocialAuthAttr $attrClient)
    {
        $user = new User([
            'name' => $attrClient->getName(),
            'surname' => $attrClient->getSurname(),
            'email' => $attrClient->getEmail(),
            'city_id' => -1,
            'has_social_creation' => 1,
            'last_visit' => time(),
        ]);
        $user->trimNames(self::MAX_NAME_LENGTH);
        $user->generatePassword(Yii::$app->params['user.socialAuthGeneratePasswordLength']);

        $transaction = $user->getDb()->beginTransaction();
        if ($user->save()) {
            $auth = new SocialAuth([
                'user_id' => $user->id,
                'source' => $attrClient->getSocialName(),
                'source_id' => $attrClient->getSocialId(),
                'screen_name' => $attrClient->getScreenName(),
            ]);
            if ($auth->save()) {
                $userInfo = new UserInfo(['user_id' => $user->id]);
                if($userInfo->save()){
                    $transaction->commit();
                    ImageHelper::saveUserPhoto($attrClient->getUserPhoto(), $user);
                }
            }
        }
        return $user;
    }

    public function createTempSocialUser(SocialAuthAttr $attrClient)
    {
        $tempUser = new TempSocialUser([
            'source' => $attrClient->getSocialName(),
            'source_id' => $attrClient->getSocialId(),
            'screen_name' => $attrClient->getScreenName(),
            'name' => $attrClient->getName(),
            'surname' => $attrClient->getSurname(),
            'email' =>  $attrClient->getEmail(),
            'url_photo' => $attrClient->getUserPhoto(),
            'hash' => Yii::$app->security->generateRandomString(50),
        ]);
        $tempUser->trimNames(self::MAX_NAME_LENGTH);
        return $tempUser->save() ? $tempUser : null;
    }
    public function createSocialBinding(SocialAuthAttr $attrClient, $userId)
    {
        $auth = new SocialAuth([
            'user_id' => $userId,
            'source' => $attrClient->getSocialName(),
            'source_id' => $attrClient->getSocialId(),
            'screen_name' => $attrClient->getScreenName(),
        ]);
        return $auth->save() ? $auth : null;
    }


    public function setRequiredFields(TempSocialUser $tempUser)
    {
        $tempUser->name = $this->name;
        $tempUser->email = $this->email;
        $tempUser->is_mail_sent = 1;
        return $tempUser->save() ? $tempUser : null;
    }
}
