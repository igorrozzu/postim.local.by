<?php

namespace app\commands\cron\notifications;

use app\models\entities\NotificationUser;
use app\models\Notification;
use app\models\Posts;
use app\models\User;
use app\models\UserInfo;
use Yii;
use yii\base\Model;
use yii\db\ActiveQuery;

class NewReview extends BaseCronNotificationHandler
{
    public function run()
    {
        $post = Posts::find()
            ->joinWith(['user a1', 'owners a2', 'usersWhoAddPostToFavorite a3'])
            ->where([Posts::tableName() . '.id' => $this->params->post_id])
            ->one();

        $redirectLink = $post->url_name . '-p' . $post->id . '?review_id=' . $this->params->id;
        $notification = new Notification([
            'message' => json_encode([
                'type' => '',
                'data' => sprintf(
                    Yii::$app->params['notificationTemplates']['common.newReview'],
                    $redirectLink,
                    $post->data
                ),
            ]),
            'sender_id' => $this->params->user_id,
            'date' => time(),
        ]);
        $notification->save();

        $userIds = $this->iterateAllUsers($post);
        $ids = array_merge($userIds->owners, $userIds->favorites);
        $dataToInsert = [];
        foreach (array_unique($ids) as $userId) {
            $dataToInsert[] = [
                'notification_id' => $notification->id,
                'user_id' => $userId,
            ];
        }

        Yii::$app->db->createCommand()
            ->batchInsert(NotificationUser::tableName(), ['notification_id', 'user_id'], $dataToInsert)
            ->execute();

        $dataToMessage = [
            'redirectLink' => Yii::$app->params['site.hostName'] . '/' . $redirectLink,
            'postName' => $post->data,
        ];
        $this->sendMessagesToUsersWithSub($userIds, $dataToMessage);
    }

    private function iterateAllUsers(Model $post): \stdClass
    {
        $userIds = new \stdClass();
        $userIds->owners = [];
        $userIds->favorites = [];

        foreach ($post->owners as $user) {
            $userIds->owners[] = $user->id;
        }

        foreach ($post->usersWhoAddPostToFavorite as $user) {
            $userIds->favorites[] = $user->id;
        }

        if (isset($post->user) && !in_array($post->user->id, $userIds->owners, true)) {
            $userIds->owners[] = $post->user->id;
        }

        return $userIds;
    }

    private function sendMessagesToUsersWithSub(\stdClass &$userIds, array &$dataToMessage): int
    {
        $messages = [];
        $userForMailSending = User::find()
            ->select([
                User::tableName() . '.name',
                User::tableName() . '.id',
                User::tableName() . '.email',
            ])
            ->innerJoinWith([
                'userInfo' => function (ActiveQuery $query) {
                    $query->where([
                        UserInfo::tableName() .
                        '.reviews_to_my_places_sub' => UserInfo::ALLOW_USER_CHOICE['yes'],
                    ]);
                },
            ], false)
            ->where(['id' => $userIds->owners]);

        if (count($userIds->favorites) > 0) {
            $favoriteUsers = User::find()
                ->select([
                    User::tableName() . '.name',
                    User::tableName() . '.id',
                    User::tableName() . '.email',
                ])
                ->innerJoinWith([
                    'userInfo' => function (ActiveQuery $query) {
                        $query->where([
                            UserInfo::tableName() .
                            '.reviews_to_favorite_places_sub' => UserInfo::ALLOW_USER_CHOICE['yes'],
                        ]);
                    },
                ], false)
                ->where(['id' => $userIds->favorites]);

            $userForMailSending->union($favoriteUsers);
        }

        $userForMailSending = $userForMailSending->all();

        foreach ($userForMailSending as $user) {
            $dataToMessage['user'] = $user;
            $messages[] = $this->mailer->compose(['html' => 'newReview'], $dataToMessage)
                ->setFrom([Yii::$app->params['mail.supportEmail'] => 'Postim.by'])
                ->setTo($user->email)
                ->setSubject('Уведомление Postim.by');
        }

        $this->mailer->sendMultiple($messages);

        return true;
    }
}