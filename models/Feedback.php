<?php

namespace app\models;

use Yii;
use yii\base\Model;


class Feedback extends Model
{

    public $message = '';
    public $email = '';
    public $subject = '';

    /**
     * @return array the validation rules.
     */

    public function rules()
    {
        return [
            [['subject'], 'required','message'=>'Ввидите тему письма'],
            [['message'], 'required','message'=>'Ввидите текст сообщения'],
            [['email'], 'required','message'=>'Введите корректный e-mail'],
            [['email'], 'email','message'=>'Введите корректный e-mail'],

        ];

    }

    public function sendSubject(){

        if($this->validate()){

            Yii::$app->mailer->useTransport('ask')->compose()
                ->setTo(Yii::$app->params['mail.feedbackEmail'])
                ->setFrom([Yii::$app->params['mail.feedbackEmail'] => $this->email])
                ->setSubject($this->subject)
                ->setTextBody($this->message)
                ->send();

            $this->clearAttr();
            return true;

        }

    }

    private function clearAttr(){
        $this->message = '';
        $this->email = '';
        $this->subject = '';
    }


}
