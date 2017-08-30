<?php

namespace app\behaviors\notification\handlers;

use app\models\News;
use app\models\Notification;
use Yii;
use yii\base\Component;
use yii\base\Object;
use yii\db\ActiveRecord;

class NewComment extends NotificationHandler
{
    public function run()
    {
        $receiveComment = $this->model::find()
            ->select([
                $this->model::tableName().'.user_id',
                $this->model::tableName().'.news_id',
                News::tableName().'.url_name',
                News::tableName().'.id',
            ])
            ->innerJoinWith('news')
            ->where([$this->model::tableName().'.id' => $this->model->receiver_comment_id])
            ->one();

        $notif = new Notification([]);
        $notif->user_id = $receiveComment->user_id;
        $notif->sender_id = $this->model->user_id;
        $notif->message = json_encode([
            'type' => '',
            'data' => sprintf(
                Yii::$app->params['notificationTemplates']['common.newComment'],
                $receiveComment->news->url_name.'-n'.$receiveComment->news->id.
                '#comment-'.$this->model->receiver_comment_id
            )
        ]);
        $notif->date = time();
        $notif->save();
    }
}