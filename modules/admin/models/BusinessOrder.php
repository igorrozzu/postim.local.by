<?php

namespace app\modules\admin\models;

use app\models\Posts;
use app\models\User;
use Yii;
use app\models\entities\BusinessOrder as ParentBusinessOrder;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "tbl_business_order".
 *
 * @property integer $user_id
 * @property integer $post_id
 * @property string $position
 * @property integer $status
 */
class BusinessOrder extends ParentBusinessOrder
{


    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }



}