<?php

namespace app\commands\cron\notifications;

use app\behaviors\notification\handlers\NotificationHandler;
use app\models\Comments;
use Yii;

class ReviewUnderComment extends BaseCronNotificationHandler
{
    public function run()
    {
        $comment = Comments::find()
            ->select([
                Comments::tableName() . '.id',
                Comments::tableName() . '.user_id',
                Comments::tableName() . '.entity_id',
            ])
            ->innerJoinWith(['review.post', 'user.userInfo'])
            ->where([Comments::tableName() . '.id' => $this->params->receiver_comment_id])
            ->one();

        $redirectLink = $comment->review->post->url_name . '-p' . $comment->review->post->id .
            '?review_id=' . $comment->review->id;

        NotificationHandler::sendNotification($comment->user_id, [
            'type' => '',
            'data' => sprintf(
                Yii::$app->params['notificationTemplates']['common.newUnderComment'],
                $redirectLink
            ),
        ], $this->params->user_id);

        if (!$comment->user->userInfo->hasAnswersToCommentsSub()) {
            return true;
        }

        return $this->mailer->compose(['html' => 'newUnderComment'], [
            'user' => $comment->user,
            'redirectLink' => Yii::$app->params['site.hostName'] . '/' . $redirectLink,
        ])->setFrom([Yii::$app->params['mail.supportEmail'] => 'Postim.by'])
            ->setTo($comment->user->email)
            ->setSubject('Уведомление Postim.by')
            ->send();
    }
}