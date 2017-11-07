<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;
use app\models\Features as ParentsModel;

/**
 * This is the model class for table "tbl_features".
 *
 * @property string $id
 * @property string $name
 * @property integer $type
 * @property integer $filter_status
 * @property string $main_features
 *
 */
class Features extends ParentsModel
{


    public function rules()
    {
        return [
            [['id', 'name', 'filter_status'], 'required'],
            [['type', 'filter_status'], 'integer'],
            [['id', 'name', 'main_features'], 'string', 'max' => 30],
            [['id'], 'unique'],
        ];
    }


    public function afterFind()
    {
        $this->trigger(self::EVENT_AFTER_FIND);
    }

}
