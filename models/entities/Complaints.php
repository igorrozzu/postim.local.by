<?php

namespace app\models\entities;

use Yii;

/**
 * This is the model class for table "tbl_complaints".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $data
 * @property string $date
 * @property integer $type
 * @property integer $status
 */
class Complaints extends \yii\db\ActiveRecord
{

    public static $PHOTO_TYPE = 1;
    public static $REVIEWS_TYPE = 2;
    public static $COMMENTS_TYPE = 3;

    public static $MODERATION_STATUS = 1;
    public static $VERIFIED_STATUS = 2;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_complaints';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'data', 'date', 'type','status','entities_id'], 'required'],
            [['user_id', 'type'], 'integer'],
            [['data'], 'string'],
            [['date'], 'safe'],
            [['user_id'], 'unique', 'targetAttribute' => ['user_id', 'entities_id','type'], 'message'=>'Ваша жалоба уже отправлена'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'data' => 'Data',
            'date' => 'Date',
            'type' => 'Type',
            'status' => 'Статус',
        ];
    }

    public function beforeValidate()
    {
        if(!$this->date){
            $this->date = time();
        }

        return parent::beforeValidate();
    }

}