<?php

namespace app\models\entities;

use Yii;

/**
 * This is the model class for table "tbl_tasks".
 *
 * @property integer $id
 * @property string $data
 * @property integer $type
 * @property integer $status
 * @property integer $date_of_execution
 */
class Task extends \yii\db\ActiveRecord
{
    const TYPE = [
        'notification' => 1,
        'accountReplenishment' => 2,
    ];

    const STATUS = [
        'waiting' => 0,
        'execution' => 1,
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_tasks';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['data', 'type'], 'required'],
            [['data'], 'string'],
            [['type', 'status', 'date_of_execution'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'data' => 'Data',
            'type' => 'Type',
            'status' => 'Status',
        ];
    }
}
