<?php

namespace app\commands\cron\notifications;

use app\behaviors\notification\handlers\NotificationHandler;
use app\models\Reviews;
use Yii;

class NewCommentToReview extends BaseCronNotificationHandler
{
    public function run()
    {
        $review = Reviews::find()
            ->innerJoinWith(['post', 'user.userInfo'])
            ->where([Reviews::tableName() . '.id' => $this->params->entity_id])
            ->one();

        if ($review->user_id === (int) $this->params->user_id) {
            return false;
        }

        $redirectLink = 'Otzyvy-' . $review->post->url_name . '-p' . $review->post->id;

        NotificationHandler::sendNotification($review->user_id, [
            'type' => '',
            'data' => sprintf(
                Yii::$app->params['notificationTemplates']['common.newCommentToReview'],
                $redirectLink
            )
        ], $this->params->user_id);

        if (!$review->user->userInfo->hasAnswersToReviewsSub()) {
            return true;
        }

        return $this->mailer->compose(['html' => 'newCommentToReview'], [
            'user' => $review->user,
            'redirectLink' => Yii::$app->params['site.hostName'] . '/' . $redirectLink,
        ])->setFrom([Yii::$app->params['mail.supportEmail'] => 'Postim.by'])
            ->setTo($review->user->email)
            ->setSubject('Уведомление Postim.by')
            ->send();
    }
}