<?php

namespace app\behaviors\notification\handlers;

use Yii;
class FillingProfile extends Reward
{
    public function run()
    {
        /*$user = $this->model::find()
            ->innerJoinWith('userInfo')
            ->where([$this->model::tableName().'.id' => $this->model->id]);*/
        $user = $this->model;

        /*$notif = new Notification([]);
        $notif->user_id = $receiveComment->user_id;
        $notif->sender_id = $this->model->user_id;
        $notif->message = json_encode([
            'type' => '',
            'data' => sprintf(
                Yii::$app->params['notificationTemplates']['common.newComment'],
                $receiveComment->news->url_name . '-n' . $receiveComment->news->id . '?comment_id='.
                $this->model->receiver_comment_id . '#comment-' . $this->model->receiver_comment_id
            )
        ]);
        $notif->date = time();
        $notif->save();*/
    }

    private function validateFillingData()
    {

    }

}