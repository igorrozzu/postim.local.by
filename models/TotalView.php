<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_total_view".
 *
 * @property integer $id
 * @property integer $count
 */
class TotalView extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_total_view';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['count'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'count' => 'Count',
        ];
    }
}
