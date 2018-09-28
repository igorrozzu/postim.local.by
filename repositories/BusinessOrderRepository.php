<?php

namespace app\repositories;

use app\models\entities\BusinessOrder;

class BusinessOrderRepository extends BusinessOrder
{
    public function increasePremium(int $duration)
    {
        $time = time();
        $period = $duration * 24 * 3600;

        if ($this->premium_finish_date <= $time) {
            $this->premium_finish_date = $time + $period;
        } else {
            $this->premium_finish_date += $period;
        }
        $this->status = self::$PREMIUM_BIZ_AC;

        return $this->update();
    }
}