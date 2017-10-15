<?php

namespace app\behaviors\notification\handlers;

use app\models\Comments;
use app\models\News;
use app\models\Notification;
use app\models\Reviews;
use Yii;
use yii\db\ActiveRecord;

class NewCommentToReview extends NotificationHandler
{
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'run'
        ];
    }

    public function run()
    {
        if (!$this->owner->isRelatedWithReviews()) {
            return false;
        }

        $review = Reviews::find()
            ->innerJoinWith(['post', 'user.userInfo'])
            ->where([Reviews::tableName() . '.id' => $this->owner->entity_id])
            ->one();

        if ($review->user_id === $this->owner->user_id) {
            return false;
        }

        $redirectLink = 'Otzyvy-' . $review->post->url_name . '-p' . $review->post->id;

        parent::sendNotification($review->user_id, [
            'type' => '',
            'data' => sprintf(
                Yii::$app->params['notificationTemplates']['common.newCommentToReview'],
                $redirectLink
            )
        ], $this->owner->user_id);

        if (!$review->user->userInfo->hasAnswersToReviewsSub()) {
            return true;
        }

        $mailer = Yii::$app->getMailer();
        $mailer->htmlLayout = 'layouts/notification';
        return $mailer->compose(['html' => 'newCommentToReview'], [
            'user' => $review->user,
            'redirectLink' => Yii::$app->request->getHostInfo() . '/' . $redirectLink,
        ])->setFrom([Yii::$app->params['mail.supportEmail'] => 'Postim.by'])
            ->setTo($review->user->email)
            ->setSubject('Уведомление Postim.by')
            ->send();
    }
}