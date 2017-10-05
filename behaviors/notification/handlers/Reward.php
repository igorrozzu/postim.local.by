<?php

namespace app\behaviors\notification\handlers;

use app\models\Notification;
use Yii;
class Reward extends NotificationHandler
{
    public function run()
    {
        $this->chargePoints();
        $notif = new Notification([]);
        $notif->user_id = $this->model->getUserId();
        $notif->message = json_encode([
            'type' => '',
            'data' => sprintf(
                Yii::$app->params['notificationTemplates'][$this->params['template']],
                $this->params['exp'],
                $this->params['money']
            )
        ]);
        $notif->date = time();
        return $notif->save();
    }

    protected function chargePoints()
    {
        $id = $this->model->getUserId();
        $p1 = $this->params['exp'];
        $p2 = $this->params['money'];
        //TODO ADD POINTS
    }
}