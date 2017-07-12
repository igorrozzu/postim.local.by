<?php

namespace app\models;

use app\components\ImageHelper;
use app\components\MailSender;
use app\components\social\attributes\SocialAuthAttributes;
use Yii;
use yii\base\Model;


class SocialRegister extends Model
{
    public $name;
    public $email;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name','email'], 'filter', 'filter' => 'trim'],
            ['email', 'unique', 'targetClass' => '\app\models\User',
                'message' => 'Пользователь с таким адресом уже существует'],
            ['email', 'email', 'message' => 'Некорректный email-адрес'],
            [['name','email'], 'required', 'message'=> 'Поле обязательно для заполнения']
        ];
    }

    public function createUser(SocialAuthAttributes $attrClient, bool $useFieldsFromForm = false)
    {
        $user = new User([
            'name' => $useFieldsFromForm ? $this->name : $attrClient->getName(),
            'email' => $useFieldsFromForm ? $this->email : $attrClient->getEmail(),
            'confirmed' => 1,
            'city_id' => -1,
        ]);
        $password = $user->generatePassword(Yii::$app->params['user.socialAuthGeneratePasswordLength']);

        $transaction = $user->getDb()->beginTransaction();
        if ($user->save()) {
            $auth = new SocialAuth([
                'user_id' => $user->id,
                'source' => $attrClient->getSocialName(),
                'source_id' => $attrClient->getSocialId(),
            ]);
            if ($auth->save()) {
                $transaction->commit();
                ImageHelper::saveUserPhoto($attrClient->getUserPhoto(), $user);
                MailSender::sendSuccessRegisterThroughSocial($user, $password);
            }
        }
        return $user;
    }

}
