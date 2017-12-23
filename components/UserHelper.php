<?php
namespace app\components;

use app\components\user\ExperienceCalc;
use app\models\entities\NotificationUser;
use app\models\entities\Task;
use app\models\Notification;
use \app\models\User;
use Yii;

class UserHelper{

    public static function sendNotification(int $userId, array $data, int $senderId = null)
    {
        $notification = new Notification([
            'message' => json_encode($data),
            'sender_id' => $senderId,
            'date' => time(),
        ]);

        if ($notification->save()) {
            $notificationUser = new NotificationUser([
                'notification_id' => $notification->id,
                'user_id' => $userId,
            ]);

            return $notificationUser->save();
        }

        return false;
    }

    public static function chargeBonuses(int $user_id, $exp, $megaMoney = null, $message,$emailMessage = null,$emailLink = null){

        $user = User::find()->with('userInfo')->where(['id'=>$user_id])->one();

        $oldLevel = $user->userInfo->level;
        $newLevel = ExperienceCalc::getLevelByExperience($user->userInfo->exp_points + $exp);

        if(!$megaMoney){
            $megaMoney = 0;
        }

        $updateResult = $user->userInfo->updateCounters([
            'exp_points' => $exp,
            'mega_money' => $megaMoney,
            'level' => $newLevel - $oldLevel,
        ]);

        if ($updateResult) {
            static::sendNotification($user_id, [
                'type' => '',
                'data' => $message,
            ]);

            if ($oldLevel !== $newLevel) {
                static::sendNotification($user_id, [
                    'type' => '',
                    'data' => sprintf(
                        Yii::$app->params['notificationTemplates']['common.newUserLevel'],
                        $newLevel
                    ),
                ]);
            }

            if (!$user->userInfo->hasExperienceAndBonusSub()) {
                return true;
            }

            if($emailLink && $emailMessage){

                self::sendMessageToEmailCustomReward($user,$emailMessage,$emailLink);
            }

        }

    }

    public static function sendMessageToEmailCustomReward($user,$emailMessage,$emailLink){
        $task = new Task([
            'data' => json_encode([
                'class' => 'SendMessageToEmail',
                'params' => [
                    'view' => ['html' => 'CustomReward'],
                    'params' => [
                        'name' => $user->name,
                        'message' => $emailMessage,
                        'url' => $emailLink
                    ],
                    'toEmail' => $user->email,
                    'subject'=>'Уведомление Postim.by'
                ],
            ]),
            'type' => Task::TYPE['notification'],
        ]);

        $task->save();
    }

    public static function sendMessageToEmail($user,$emailMessage,$emailLink){
        $task = new Task([
            'data' => json_encode([
                'class' => 'SendMessageToEmail',
                'params' => [
                    'view' => ['html' => 'emailMessage'],
                    'params' => [
                        'name' => $user->name,
                        'message' => $emailMessage,
                        'url' => $emailLink
                    ],
                    'toEmail' => $user->email,
                    'subject'=>'Уведомление Postim.by'
                ],
            ]),
            'type' => Task::TYPE['notification'],
        ]);

        $task->save();

        $lol =3 ;
    }




}