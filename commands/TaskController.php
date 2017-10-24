<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;
use app\behaviors\notification\handlers\NotificationHandler;
use app\components\user\ExperienceCalc;
use app\models\entities\NotificationUser;
use app\models\entities\Task;
use app\models\Notification;
use app\models\User;
use app\models\UserInfo;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;


/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class TaskController extends Controller
{
    public function actionHandleNotifications()
    {
        $tasksQuery = Task::find()->where([
            Task::tableName() . '.type' => Task::TYPE['notification']
        ]);

        $mailer = Yii::$app->getMailer();
        $mailer->htmlLayout = 'layouts/notification';
        $ids = [];

        foreach ($tasksQuery->each() as $task) {
            try {
                $ids[] = $task->id;
                $data = json_decode($task->data);
                $class = 'app\commands\cron\notifications\\' . $data->class;
                $handler = new $class($mailer);
                $handler->params = $data->params;
                $handler->run();
            } catch (\Exception $e) {
                continue;
            }
        }

        if (count($ids) > 0) {
            Task::deleteAll([Task::tableName() . '.id' => $ids]);
        }

        return true;
    }

    public function actionChargeEverydayBonus()
    {
        $dayBefore = time() - 3600 * 24;
        $usersQuery = User::find()
            ->select([
                User::tableName() . '.id',
                User::tableName() . '.last_visit',
            ])
            ->innerJoinWith('userInfo')
            ->where(['>=', User::tableName() . '.last_visit', $dayBefore]);

        $template = Yii::$app->params['notificationTemplates']['reward.everyday'];
        $message = sprintf($template['text'], $template['exp'], $template['money']);

        $userIds = new \stdClass();
        $transaction = Yii::$app->db->beginTransaction();

        $notification = new Notification([
            'message' => json_encode([
                'type' => '',
                'data' => $message,
            ]),
            'date' => time(),
        ]);
        $notification->save();

        try {
            foreach ($usersQuery->each() as $user) {
                $userIds->all[] = $user->id;
                $userIds->dataToInsert[] = [
                    'notification_id' => $notification->id,
                    'user_id' => $user->id,
                ];
                $oldLevel = $user->userInfo->level;
                $newLevel = ExperienceCalc::getLevelByExperience(
                    $user->userInfo->exp_points + $template['exp']);

                if ($oldLevel !== $newLevel) {
                    NotificationHandler::sendNotification($user->id, [
                        'type' => '',
                        'data' => sprintf(
                            Yii::$app->params['notificationTemplates']['common.newUserLevel'],
                            $newLevel
                        ),
                    ]);
                    $userIds->levelUpdate[] = $user->id;
                }

                if ($user->userInfo->hasExperienceAndBonusSub()) {
                    $userIds->mailSending[] = $user->id;
                }
            }

            if (isset($userIds->all)) {
                UserInfo::updateAllCounters([
                    'exp_points' => $template['exp'],
                    'mega_money' => $template['money'],
                ], [UserInfo::tableName() . '.user_id' => $userIds->all]);

                if (isset($userIds->levelUpdate)) {
                    UserInfo::updateAllCounters([
                        'level' => 1,
                    ], [UserInfo::tableName() . '.user_id' => $userIds->levelUpdate]);
                }

                Yii::$app->db->createCommand()
                    ->batchInsert(NotificationUser::tableName(), ['notification_id', 'user_id'],
                        $userIds->dataToInsert)
                    ->execute();
            }

            $transaction->commit();
        } catch(\Throwable $e) {
            $transaction->rollBack();

            return false;
        }

        if (isset($userIds->mailSending)) {
            $mailer = Yii::$app->getMailer();
            $mailer->htmlLayout = 'layouts/notification';

            $messages = [];
            $usersQueryForMailSending = User::find()
                ->select([
                    User::tableName() . '.name',
                    User::tableName() . '.id',
                    User::tableName() . '.email',
                ])
                ->where(['id' => $userIds->mailSending]);

            foreach ($usersQueryForMailSending->each() as $user) {
                $messages[] = $mailer->compose(['html' => 'reward'], [
                    'user' => $user,
                    'message' => $message,
                ])->setFrom([Yii::$app->params['mail.supportEmail'] => 'Postim.by'])
                    ->setTo($user->email)
                    ->setSubject('Уведомление Postim.by');
            }

            $mailer->sendMultiple($messages);
        }

        return true;
    }
}
