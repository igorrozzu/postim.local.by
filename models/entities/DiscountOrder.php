<?php

namespace app\models\entities;

use app\models\Discounts;
use app\models\User;
use Yii;

/**
 * This is the model class for table "tbl_discount_order".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $discount_id
 * @property integer $date_buy
 * @property string $promo_code
 * @property integer $pin_code
 * @property integer $status_promo
 * @property integer $date_finish
 * @property Discounts $discount
 * @property User $user
 */
class DiscountOrder extends \yii\db\ActiveRecord
{
    const TYPE = ['promoCode' => 1, 'certificate' => 2];
    const STATUS = ['active' => 1, 'inactive' => 0];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_discount_order';
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

    public static function getAllCount($type)
    {
        return self::find()
            ->innerJoinWith(['discount.ownerPost'], false)
            ->onCondition([OwnerPost::tableName() . '.owner_id' => \Yii::$app->user->id])
            ->andWhere([Discounts::tableName() . '.type' => $type])
            ->count();
    }
    public static function getActiveCount($type, $status)
    {
        return self::find()
            ->innerJoinWith(['discount.ownerPost'], false)
            ->onCondition([OwnerPost::tableName() . '.owner_id' => \Yii::$app->user->id])
            ->andWhere([Discounts::tableName() . '.type' => $type])
            ->andWhere([DiscountOrder::tableName() . '.status_promo' => $status])
            ->count();
    }

    public function isActive(): bool
    {
        return (bool)$this->status_promo;
    }

    public function isInactive(): bool
    {
        return !$this->isActive();
    }
}
