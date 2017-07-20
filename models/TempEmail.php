<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_temp_emails".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $hash
 * @property string $email
 */
class TempEmail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_temp_emails';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'hash', 'email'], 'required'],
            [['user_id'], 'integer'],
            [['hash'], 'string', 'max' => 50],
            [['email'], 'string', 'max' => 80],
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
            'hash' => 'Hash',
            'email' => 'Email',
        ];
    }
}
