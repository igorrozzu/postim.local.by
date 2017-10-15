<?php

namespace app\behaviors\notification\handlers;

use app\models\entities\NotificationUser;
use app\models\Notification;
use app\models\Posts;
use app\models\User;
use app\models\UserInfo;
use Yii;
use yii\base\Model;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\mail\MailerInterface;
use yii\mail\MessageInterface;

class NewReview extends NotificationHandler
{

    private $notificationUserHeaders = ['notification_id', 'user_id'];
    private $mailer;

    /**
     * NewReview constructor.
     */
    public function __construct()
    {
        $this->mailer = Yii::$app->getMailer();
        $this->mailer->htmlLayout = 'layouts/notification';

        parent::__construct();
    }

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'run'
        ];
    }

    public function run()
    {
        $post = Posts::find()
            ->joinWith(['user a1', 'owners a2', 'usersWhoAddPostToFavorite a3'])
            ->where([Posts::tableName() . '.id' => $this->owner->post_id])
            ->one();

        $redirectLink = $post->url_name . '-p' . $post->id . '?review_id=' . $this->owner->id;
        $notification = new Notification([
            'message' => json_encode([
                'type' => '',
                'data' => sprintf(
                    Yii::$app->params['notificationTemplates']['common.newReview'],
                    $redirectLink,
                    $post->data
                ),
            ]),
            'sender_id' => $this->owner->user_id,
            'date' => time(),
        ]);
        $notification->save();

        $userIds = $this->iterateAllUsers($post);
        $dataToInsert = [];
        foreach ($userIds as $userId) {
            $dataToInsert[] = [
                'notification_id' => $notification->id,
                'user_id' => $userId,
            ];
        }

        Yii::$app->db->createCommand()
            ->batchInsert(NotificationUser::tableName(), $this->notificationUserHeaders, $dataToInsert)
            ->execute();

        $dataToMessage = [
            'redirectLink' => Yii::$app->request->getHostInfo() . '/' . $redirectLink,
            'postName' => $post->data,
        ];
        $this->sendMessagesToUsersWithSub($userIds, $dataToMessage);
    }

    private function iterateAllUsers(Model $post): array
    {
        $userIds[] = $post->user->id;

        foreach ($post->usersWhoAddPostToFavorite as $user) {
            $userIds[] = $user->id;
        }

        foreach ($post->owners as $user) {
            $userIds[] = $user->id;
        }

        return array_unique($userIds);
    }

    private function sendMessagesToUsersWithSub(array &$userIds, array &$dataToMessage): int
    {
        $messages = [];
        $userForMailSending = User::find()
            ->innerJoinWith(['userInfo' => function(ActiveQuery $query) {
                $query->where([UserInfo::tableName() .
                    '.reviews_and_comments_to_places_sub' => UserInfo::ALLOW_USER_CHOICE['yes']]);
            }])
            ->where(['id' => $userIds])
            ->all();

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