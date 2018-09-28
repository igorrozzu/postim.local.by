<?php

namespace app\models\entities;

use app\models\Discounts;
use app\models\User;
use Yii;

/**
 * This is the model class for table "tbl_favorites_discount".
 *
 * @property integer $user_id
 * @property integer $discount_id
 *
 * @property Discounts $discount
 * @property User $user
 */
class FavoritesDiscount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_favorites_discount';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'discount_id'], 'required'],
            [['user_id', 'discount_id'], 'integer'],
            [
                ['discount_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Discounts::className(),
                'targetAttribute' => ['discount_id' => 'id'],
            ],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => ['user_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'discount_id' => 'Discount ID',
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
