<?php

namespace app\behaviors\notification\handlers;

use app\models\Comments;
use app\models\News;
use app\models\Notification;
use Yii;
use yii\db\ActiveRecord;

class NewUnderComment extends NotificationHandler
{
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
        if (!$this->isValid()) {
            return false;
        }

        if ($this->owner->isRelatedWithNews()) {
            return $this->handleNewsUnderComment();
        } else if ($this->owner->isRelatedWithReviews()) {
            return $this->handleReviewUnderComment();
        }

        return false;
    }

    public function handleNewsUnderComment()
    {
        $comment = Comments::find()
            ->select([
                Comments::tableName() . '.user_id',
                Comments::tableName() . '.entity_id',
                News::tableName() . '.url_name',
                News::tableName() . '.id',
            ])
            ->innerJoinWith(['news', 'user.userInfo'])
            ->where([Comments::tableName() . '.id' => $this->owner->receiver_comment_id])
            ->one();

        $redirectLink = $comment->news->url_name . '-n' . $comment->news->id . '?comment_id='.
            $this->owner->receiver_comment_id . '#comment-' . $this->owner->receiver_comment_id;

        parent::sendNotification($comment->user_id, [
            'type' => '',
            'data' => sprintf(
                Yii::$app->params['notificationTemplates']['common.newUnderComment'],
                $redirectLink
            )
        ], $this->owner->user_id);

        if (!$comment->user->userInfo->hasAnswersToCommentsSub()) {
            return true;
        }

        return $this->mailer->compose(['html' => 'newUnderComment'], [
            'user' => $comment->user,
            'redirectLink' => Yii::$app->request->getHostInfo() . '/' . $redirectLink,
        ])->setFrom([Yii::$app->params['mail.supportEmail'] => 'Postim.by'])
            ->setTo($comment->user->email)
            ->setSubject('Уведомление Postim.by')
            ->send();
    }

    public function handleReviewUnderComment()
    {
        $comment = Comments::find()
            ->select([
                Comments::tableName() . '.id',
                Comments::tableName() . '.user_id',
                Comments::tableName() . '.entity_id',
            ])
            ->innerJoinWith(['review.post', 'user.userInfo'])
            ->where([Comments::tableName() . '.id' => $this->owner->receiver_comment_id])
            ->one();

        $redirectLink = 'Otzyvy-' . $comment->review->post->url_name . '-p' . $comment->review->post->id .
            '?review_id=' . $comment->review->id . '&comment_id='. $comment->id;

        parent::sendNotification($comment->user_id, [
            'type' => '',
            'data' => sprintf(
                Yii::$app->params['notificationTemplates']['common.newUnderComment'],
                $redirectLink
            )
        ], $this->owner->user_id);

        if (!$comment->user->userInfo->hasAnswersToCommentsSub()) {
            return true;
        }

        return $this->mailer->compose(['html' => 'newUnderComment'], [
            'user' => $comment->user,
            'redirectLink' => Yii::$app->request->getHostInfo() . '/' . $redirectLink,
        ])->setFrom([Yii::$app->params['mail.supportEmail'] => 'Postim.by'])
            ->setTo($comment->user->email)
            ->setSubject('Уведомление Postim.by')
            ->send();
    }

    private function isValid()
    {
        return $this->owner->getScenario() === Comments::$ADD_UNDER_COMMENT;
    }
}