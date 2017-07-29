<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_users_promo".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $discount_id
 * @property integer $date_buy
 * @property integer $date_finish
 * @property string $promo_code
 * @property integer $pin_code
 * @property integer $status_promo
 *
 * @property Discounts $discount
 * @property User $user
 */
class UsersPromo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_users_promo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'discount_id', 'date_buy', 'date_finish', 'promo_code', 'status_promo'], 'required'],
            [['user_id', 'discount_id', 'date_buy', 'date_finish', 'pin_code', 'status_promo'], 'integer'],
            [['promo_code'], 'string', 'max' => 200],
            [['discount_id'], 'exist', 'skipOnError' => true, 'targetClass' => Discounts::className(), 'targetAttribute' => ['discount_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
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
            'discount_id' => 'Discount ID',
            'date_buy' => 'Date Buy',
            'date_finish' => 'Date Finish',
            'promo_code' => 'Promo Code',
            'pin_code' => 'Pin Code',
            'status_promo' => 'Status Promo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiscount()
    {
        return $this->hasOne(Discounts::className(), ['id' => 'discount_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
