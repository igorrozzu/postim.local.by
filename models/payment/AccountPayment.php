<?php

namespace app\models\payment;

use yii\db\ActiveRecord;

/**
 * This is the model class for form "AccountPayment".
 *
 * @property integer $type
 * @property string $money
 */
class AccountPayment extends ActiveRecord
{
    const PAYMENT_TYPE = ['erip' => 1, 'card' => 2];

    public static function tableName()
    {
        return 'tbl_account_payment';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['money', 'required', 'message' => 'Необходимо указать сумму платежа'],
            ['entity', 'required'],
            [['entity', 'status', 'user_id'], 'integer'],
            [['money'], 'number'],
            ['money', 'compare', 'compareValue' => 0, 'operator' => '>'],
            ['entity', 'in', 'range' => array_values(AccountPayment::PAYMENT_TYPE)],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'money' => 'Сумма',
            'type' => 'Cпособ оплаты',
        ];
    }
}
